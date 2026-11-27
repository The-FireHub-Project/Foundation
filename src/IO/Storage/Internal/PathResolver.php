<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Storage\Internal;

use FireHub\Core\Boundary\IO\Storage\Path as PathContract;
use FireHub\Core\Meta\Enum\Side;
use FireHub\Core\Foundation\Constant\Environment\Php\ {
    Paths, PlatformInfo
};
use FireHub\Foundation\IO\Storage\Path;
use FireHub\Foundation\IO\Storage\Exception\ {
    InvalidObjectTypeException, InvalidPathException, InvalidRootDirectoryException, ObjectNotFoundException,
    PathOutsideRootException, SymbolicLinkNotAllowedException
};
use FireHub\Runtime;

/**
 * ### Storage path resolver
 *
 * Resolves logical storage paths into physical filesystem paths within a configured root directory. Validates
 * platform-specific path restrictions and prevents resolution outside the storage root.
 *
 * <code>
 * $resolver = new PathResolver(__DIR__.'/storage');
 * $path = $resolver->resolve(new Path('documents/example.txt'));
 * </code>
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class PathResolver {

    /**
     * ### Canonical storage root directory
     * @since 1.0.0
     *
     * @var non-empty-string
     */
    private string $root;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To determine whether the root directory exists.
     * @uses \FireHub\Runtime\FileSystem\Path::absolute() To resolve the canonical root directory.
     *
     * @param non-empty-string $root <p>
     * Physical storage root directory.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidRootDirectoryException If the root directory is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     *
     * @return void
     */
    public function __construct (string $root) {

        if ($root === '' || !Runtime\FileSystem\Folder::exists($root))
            throw new InvalidRootDirectoryException;

        $this->root = Runtime\FileSystem\Path::absolute($root);

    }

    /**
     * ### Gets the storage root directory
     *
     * <code>
     * $root = $resolver->root();
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-empty-string Canonical absolute storage root directory.
     */
    public function root ():string {

        return $this->root;

    }

    /**
     * ### Resolves a logical storage path
     *
     * Resolves a logical storage path into a physical filesystem path within the configured storage root directory.
     *
     * Existing path components are checked individually to prevent traversal through symbolic links or incompatible
     * filesystem objects.
     *
     * The destination object is not required to exist.
     *
     * <code>
     * $resolver = new PathResolver(__DIR__.'/storage');
     *
     * $path = $resolver->resolve(
     *     new \FireHub\Foundation\IO\Storage\Path('documents/report.pdf')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Path::segments() To retrieve logical path segments.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::validateSegment() To validate platform-specific
     * path restrictions.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::contains() To verify the resolved path boundary.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To normalize directory separators.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To invalidate cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\Link::symbolicExists() To detect symbolic links.
     * @uses \FireHub\Runtime\FileSystem\File::exist() To determine whether a filesystem object exists.
     * @uses \FireHub\Runtime\FileSystem\Path::absolute() To resolve existing filesystem paths.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To validate intermediate directories.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical storage path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return non-empty-string Resolved physical filesystem path.
     */
    public function resolve (PathContract $path):string {

        $path = $path instanceof Path
            ? $path
            : new Path($path->value());

        $segments = $path->segments();

        $resolved = $this->root;

        $last = Runtime\Arr\Inspection::count($segments) - 1;

        foreach ($segments as $index => $segment) {

            $this->validateSegment($segment);

            $resolved = Runtime\Str\SB\Transform::trim(
                    $resolved,
                    Side::RIGHT,
                    Paths::DIRECTORY_SEPARATOR
                ).Paths::DIRECTORY_SEPARATOR.$segment;

            Runtime\FileSystem\Metadata::clearCache(true, $resolved);

            if (Runtime\FileSystem\Link::symbolicExists($resolved))
                throw new SymbolicLinkNotAllowedException(
                    context: [
                        'path' => $path->value()
                    ]
                );

            if (Runtime\FileSystem\File::exist($resolved)) {

                $resolved = Runtime\FileSystem\Path::absolute($resolved);

                if ($index !== $last && !Runtime\FileSystem\Folder::exists($resolved))
                    throw new InvalidObjectTypeException(
                        context: [
                            'path' => $path->value()
                        ]
                    );

            }

            if (!$this->contains($resolved))
                throw new PathOutsideRootException;

        }

        return $resolved;

    }

    /**
     * ### Resolves a storage file path
     *
     * Resolves a logical storage path that identifies either an existing regular file or a nonexistent destination.
     *
     * <code>
     * $resolver = new PathResolver(__DIR__.'/storage');
     *
     * $destination = $resolver->resolveFile(
     *     new \FireHub\Foundation\IO\Storage\Path('new-file.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the logical storage path.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To invalidate cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\File::exist() To determine whether the filesystem object exists.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To validate the filesystem object type.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical storage file path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return non-empty-string Physical storage file path.
     */
    public function resolveFile (PathContract $path):string {

        $resolved = $this->resolve($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        if (
            Runtime\FileSystem\File::exist($resolved)
            && !Runtime\FileSystem\File::isFile($resolved)
        ) throw new InvalidObjectTypeException(
            context: [
                'path' => $path->value()
            ]
        );

        return $resolved;

    }

    /**
     * ### Locates an existing stored object
     *
     * Resolves the canonical absolute filesystem path of an existing stored object.
     *
     * <code>
     * $path = $resolver->locate(new Path('documents/example.txt'));
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of an existing stored object.
     * </p>
     *
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear the filesystem metadata cache.
     * @uses \FireHub\Runtime\FileSystem\File::exist() To check if the path is a file.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To check if the path is a regular file.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return non-empty-string Canonical absolute filesystem path of the stored object.
     */
    public function locate (PathContract $path):string {

        $resolved = $this->resolve($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        if (!Runtime\FileSystem\File::exist($resolved))
            throw new ObjectNotFoundException(
                context: [
                    'path' => $path->value()
                ]
            );

        if (!Runtime\FileSystem\File::isFile($resolved))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $path->value()
                ]
            );

        return $resolved;

    }

    /**
     * ### Locates an existing storage directory
     *
     * Resolves the logical storage path and ensures that it identifies an existing directory.
     *
     * <code>
     * $resolver = new PathResolver(__DIR__.'/storage');
     *
     * $directory = $resolver->locateDirectory(
     *     new \FireHub\Foundation\IO\Storage\Path('documents')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the logical storage path.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To invalidate cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\File::exist() To determine whether the filesystem object exists.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To validate the directory type.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the storage directory.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return non-empty-string Physical path of the storage directory.
     */
    public function locateDirectory (PathContract $path):string {

        $resolved = $this->resolve($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        if (!Runtime\FileSystem\File::exist($resolved))
            throw new ObjectNotFoundException(
                context: [
                    'path' => $path->value()
                ]
            );

        if (!Runtime\FileSystem\Folder::exists($resolved))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $path->value()
                ]
            );

        return $resolved;

    }

    /**
     * ### Determines whether a physical path is within the storage root
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To remove the trailing directory separator.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the path starts with the storage root.
     * @uses \FireHub\Core\Foundation\Constant\Environment\Php\Paths::DIRECTORY_SEPARATOR To check for directory
     * separators.
     *
     * @param non-empty-string $path <p>
     * Physical filesystem path.
     * </p>
     *
     * @return bool True if the physical path is within the storage root, false otherwise.
     */
    private function contains (string $path):bool {

        $prefix = Runtime\Str\SB\Transform::trim(
            $this->root,
            Side::RIGHT,
            Paths::DIRECTORY_SEPARATOR
            ).Paths::DIRECTORY_SEPARATOR;

        return Runtime\Str\SB\Search::startsWith($prefix, $path);

    }

    /**
     * ### Validates a platform-specific path segment
     *
     * Rejects filesystem names that are not supported by the current operating system.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Constant\Environment\Php\PlatformInfo::OS_FAMILY To check the operating system.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check for invalid characters.
     *
     * @param non-empty-string $segment <p>
     * Logical storage path segment.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the path segment is invalid.
     *
     * @return void
     */
    private function validateSegment (string $segment):void {

        if (PlatformInfo::OS_FAMILY !== 'Windows') return;

        if (
            Runtime\Str\SB\Regex::match('/[<>:"|?*]/', $segment)
            || Runtime\Str\SB\Regex::match('/[. ]\z/', $segment)
            || Runtime\Str\SB\Regex::match(
                '/\A(?:CON|PRN|AUX|NUL|COM[1-9¹²³]|LPT[1-9¹²³])(?:\.|\z)/iu',
                $segment
            )
        ) throw new InvalidPathException;

    }

}