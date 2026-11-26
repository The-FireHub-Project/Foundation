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

namespace FireHub\Foundation\IO\Storage\Driver;

use FireHub\Core\Boundary\IO\Storage\Path;
use FireHub\Core\Boundary\IO\Storage\Driver\ {
    Locatable, Readable, Writable
};
use FireHub\Core\Boundary\IO\Stream\Readable as ReadableStream;
use FireHub\Foundation\IO\Storage\Internal\PathResolver;
use FireHub\Foundation\IO\Stream\ {
    Copier, File
};
use FireHub\Runtime;

/**
 * ### Local filesystem storage driver
 *
 * Provides persistent storage of objects within a configured local filesystem directory.
 *
 * Supports reading, writing, deleting, and locating stored objects.
 *
 * All logical paths are resolved relative to the configured storage root.
 *
 * <code>
 * $driver = new Local(__DIR__.'/storage');
 *
 * $driver->put(
 *     new \FireHub\Foundation\IO\Storage\Path('example.txt'),
 *     'Contents'
 * );
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Local implements Readable, Writable, Locatable {

    /**
     * ### Storage path resolver
     * @since 1.0.0
     */
    private PathResolver $resolver;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param non-empty-string $root <p>
     * Existing local filesystem directory used as the storage root.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidRootDirectoryException If the root directory is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     *
     * @return void
     */
    public function __construct (string $root) {

        $this->resolver = new PathResolver($root);

    }

    /**
     * ### Determines whether a stored object exists
     *
     * Checks whether the logical storage path identifies an existing regular file.
     *
     * <code>
     * $exists = $driver->exists(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the physical filesystem path.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear the filesystem metadata cache.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To determine whether the resolved path is a regular file.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the path segment is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path points outside
     * the storage root directory.
     *
     * @return bool True if the stored object exists, false otherwise.
     */
    public function exists (Path $path):bool {

        $resolved = $this->resolver->resolve($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        return Runtime\FileSystem\File::isFile($resolved);

    }

    /**
     * ### Gets the contents of a stored object
     *
     * Reads the complete contents of an existing stored object.
     *
     * Supports textual and binary content, including empty files.
     *
     * <code>
     * $contents = $driver->get(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locate() To resolve the existing stored object.
     * @uses \FireHub\Runtime\FileSystem\File::getContent() To read the contents of the file.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\ReadFileException If we can't get content from a path.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the path segment is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path
     * points outside the storage root directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ObjectNotFoundException If the stored object does not exist.
     *
     * @return string Complete contents of the stored object.
     */
    public function get (Path $path):string {

        return Runtime\FileSystem\File::getContent(
            $this->resolver->locate($path)
        );

    }

    /**
     * ### Stores the contents of an object
     *
     * Creates a stored object or replaces its existing contents.
     *
     * The parent directory must already exist.
     *
     * <code>
     * $driver->put(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt'),
     *     'Contents'
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the destination path.
     * @uses \FireHub\Runtime\FileSystem\File::putContent() To store the contents of the object.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     * @param string $contents <p>
     * Contents to store, including binary data.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\FileNotFoundException If $path is not file.
     * @throws \FireHub\Runtime\Exception\FileWriteException If the $create_file option is off or couldn't put
     * content on a path.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the path segment is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path points outside
     * the storage root directory.
     *
     * @return void
     */
    public function put (Path $path, string $contents):void {

        Runtime\FileSystem\File::putContent(
            $this->resolver->resolve($path),
            $contents
        );

    }

    /**
     * ### Deletes a stored object
     *
     * Deletes an existing stored object.
     *
     * Missing objects are ignored.
     *
     * <code>
     * $driver->delete(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the physical filesystem path.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear the filesystem metadata cache.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To determine whether the resolved path is a regular file.
     * @uses \FireHub\Runtime\FileSystem\File::delete() To delete the stored object.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the path segment is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path points outside
     * the storage root directory.
     * @throws \FireHub\Runtime\Exception\DeleteFileException If we couldn't delete the file.
     *
     * @return void
     */
    public function delete (Path $path):void {

        $resolved = $this->resolver->resolve($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        if (!Runtime\FileSystem\File::isFile($resolved))
            return;

        Runtime\FileSystem\File::delete($resolved);

    }

    /**
     * ### Opens a readable stream for a stored object
     *
     * Opens an existing stored object as a readable binary stream.
     *
     * The caller owns the returned stream and must close it.
     *
     * <code>
     * $stream = $driver->readStream(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
     * );
     *
     * try {
     *     $contents = $stream->contents();
     * } finally {
     *     $stream->close();
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locate() To locate the stored object.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the file cannot be opened.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the path segment is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path
     * points outside the storage root directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ObjectNotFoundException If the stored object does not exist.
     *
     * @return \FireHub\Core\Boundary\IO\Stream\Readable Readable file stream.
     */
    public function readStream (Path $path):ReadableStream {

        return new File(
            $this->resolver->locate($path),
            Runtime\Stream\Enum\OpenMode::READ_BINARY
        );

    }

    /**
     * ### Stores the contents of a readable stream
     *
     * Copies the remaining contents of a readable stream into a stored object.
     *
     * The destination object is created or replaced.
     *
     * The source stream remains owned by the caller.
     *
     * <code>
     * $source = $driver->readStream(
     *     new \FireHub\Foundation\IO\Storage\Path('source.txt')
     * );
     *
     * try {
     *     $driver->writeStream(
     *         new \FireHub\Foundation\IO\Storage\Path('backup.txt'),
     *         $source
     *     );
     * } finally {
     *     $source->close();
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the destination path.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copy() To transfer stream contents.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::close() To close the source stream.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical destination path.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Source stream to copy.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream cannot be copied.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the path segment is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path points outside
     * the storage root directory.
     *
     * @return void
     */
    public function writeStream (Path $path, ReadableStream $stream):void {

        $destination = new File(
            $this->resolver->resolve($path),
            Runtime\Stream\Enum\OpenMode::WRITE_BINARY
        );

        try {

            Copier::copy($stream, $destination);

        } finally {

            $destination->close();

        }

    }

    /**
     * ### Resolves the physical path of a stored object
     *
     * Returns the canonical absolute path of an existing stored object.
     *
     * <code>
     * $path = $driver->locate(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locate() To locate the stored object.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the path segment is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path
     * points outside the storage root directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ObjectNotFoundException If the stored object does not exist.
     */
    public function locate (Path $path):string {

        return $this->resolver->locate($path);

    }

}