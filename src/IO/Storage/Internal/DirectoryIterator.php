<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Storage\Internal;

use FireHub\Core\Boundary\IO\Storage\Path as PathContract;
use FireHub\Core\Foundation\Constant\Environment\Php\Paths;
use FireHub\Foundation\IO\Storage\Path;
use FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException;
use FireHub\Runtime;

/**
 * ### Storage directory iterator
 *
 * Provides lazy traversal of physical storage directories.
 *
 * Returned logical paths are relative to the storage root directory. Symbolic links, reserved temporary files, and
 * unsupported filesystem object types are excluded from traversal results.
 *
 * Directory traversal does not provide a consistent filesystem snapshot.
 * @since 1.0.0
 *
 * @internal
 */
final readonly class DirectoryIterator {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\IO\Storage\Internal\PathResolver $resolver <p>
     * Storage filesystem path resolver.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private PathResolver $resolver
    ) {}

    /**
     * ### Lists stored files
     *
     * Lazily lists regular files within the specified storage directory.
     *
     * If no directory is specified, traversal starts from the storage root. Missing directories produce an empty
     * result.
     *
     * <code>
     * foreach ($iterator->files(new Path('documents'), true) as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryIterator::iterate() To traverse the storage directory.
     *
     * @param null|\FireHub\Core\Boundary\IO\Storage\Path $directory [optional] <p>
     * Logical directory path or null to traverse the storage root.
     * </p>
     * @param bool $recursive [optional] <p>
     * Whether files within descendant directories should be included.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the starting directory is not a
     * directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return iterable<int, \FireHub\Foundation\IO\Storage\Path> Logical paths of stored files.
     */
    public function files (?PathContract $directory = null, bool $recursive = false):iterable {

        return $this->iterate($directory, $recursive, false);

    }

    /**
     * ### Lists storage directories
     *
     * Lazily lists directories within the specified storage directory.
     *
     * The specified directory itself is not included in the result. Missing directories produce an empty result.
     *
     * <code>
     * foreach ($iterator->directories(new Path('documents'), true) as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryIterator::iterate() To traverse the storage directory.
     *
     * @param null|\FireHub\Core\Boundary\IO\Storage\Path $directory [optional] <p>
     * Logical directory path or null to traverse the storage root.
     * </p>
     * @param bool $recursive [optional] <p>
     * Whether all descendant directories should be included.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the starting directory is not a
     * directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return iterable<int, \FireHub\Foundation\IO\Storage\Path> Logical paths of storage directories.
     */
    public function directories (?PathContract $directory = null, bool $recursive = false):iterable {

        return $this->iterate($directory, $recursive, true);

    }

    /**
     * ### Traverses a storage directory
     *
     * Resolves the starting directory and initializes filesystem traversal.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::root() To retrieve the physical storage root
     * directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the physical directory path.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\File::exist() To determine whether the filesystem object exists.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To determine whether the filesystem object is a directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryIterator::walk() To traverse the resolved directory.
     *
     * @param null|\FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical starting directory or null for the storage root.
     * </p>
     * @param bool $recursive <p>
     * Whether descendant directories should be traversed.
     * </p>
     * @param bool $directories <p>
     * Whether directories rather than regular files should be returned.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the starting directory is not a
     * directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return iterable<int, \FireHub\Foundation\IO\Storage\Path> Logical paths of matching filesystem objects.
     */
    private function iterate (?PathContract $directory, bool $recursive, bool $directories):iterable {

        $directory = match (true) {
            $directory === null => null,
            $directory instanceof Path => $directory,
            default => new Path($directory->value())
        };

        $physical = $directory === null
            ? $this->resolver->root()
            : $this->resolver->resolve($directory);

        Runtime\FileSystem\Metadata::clearCache(true, $physical);

        if (!Runtime\FileSystem\File::exist($physical))
            return;

        if (!Runtime\FileSystem\Folder::exists($physical))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $directory?->value()
                ]
            );

        foreach ($this->walk(
            $physical,
            $directory,
            $recursive,
            $directories
        ) as $path)
            yield $path;

    }

    /**
     * ### Walks a physical storage directory
     *
     * Lists directory entries, validates logical paths, and optionally traverses descendant directories.
     *
     * Symbolic links and reserved temporary filenames are excluded.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\FileSystem\Folder::list() To enumerate physical directory entries.
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::isTemporary() To identify reserved temporary
     * filenames.
     * @uses \FireHub\Foundation\IO\Storage\Path::join() To construct descendant logical paths.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\Link::symbolicExists() To identify symbolic links.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To validate and resolve descendant
     * filesystem paths.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To identify directories.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To identify regular files.
     *
     * @param non-empty-string $physical <p>
     * Physical directory path.
     * </p>
     * @param null|\FireHub\Foundation\IO\Storage\Path $logical <p>
     * Logical directory path or null for the storage root.
     * </p>
     * @param bool $recursive <p>
     * Whether descendant directories should be traversed.
     * </p>
     * @param bool $directories <p>
     * Whether directories rather than regular files should be returned.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Runtime\Exception\CannotListFolderException If $folder is empty, or we couldn't list files
     * and directories inside the specified folder.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return iterable<int, \FireHub\Foundation\IO\Storage\Path> Logical paths of matching filesystem objects.
     */
    private function walk (string $physical, ?Path $logical, bool $recursive, bool $directories):iterable {

        foreach (Runtime\FileSystem\Folder::list($physical) as $name) {

            /** @var non-empty-string $name */
            if ($name === '.' || $name === '..' || AtomicWriter::isTemporary($name))
                continue;

            $candidate = $physical.Paths::DIRECTORY_SEPARATOR.$name;

            Runtime\FileSystem\Metadata::clearCache(true, $candidate);

            if (Runtime\FileSystem\Link::symbolicExists($candidate))
                continue;

            $path = $logical === null
                ? new Path($name)
                : $logical->join($name);

            $resolved = $this->resolver->resolve($path);

            if (Runtime\FileSystem\Folder::exists($resolved)) {

                if ($directories)
                    yield $path;

                if ($recursive)
                    foreach ($this->walk(
                        $resolved,
                        $path,
                        true,
                        $directories
                    ) as $descendant)
                        yield $descendant;

            } elseif (!$directories && Runtime\FileSystem\File::isFile($resolved))

                yield $path;

        }

    }

}