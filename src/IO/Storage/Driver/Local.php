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

namespace FireHub\Foundation\IO\Storage\Driver;

use FireHub\Core\Boundary\IO\Storage\Path;
use FireHub\Core\Boundary\IO\Storage\Driver\ {
    Appendable, Copyable, Listable, Locatable, MetadataAware, Movable, Readable,
    DirectoryCopyable, DirectoryMovable, DirectoryWritable
};
use FireHub\Core\Boundary\IO\Stream\ {
    Readable as ReadableStream, Writable as WritableStream
};
use FireHub\Core\Foundation\Constant\Environment\Php\PlatformInfo;
use FireHub\Foundation\IO\Storage\ {
    Permissions, Metadata
};
use FireHub\Foundation\IO\Storage\Internal\ {
    AtomicWriter, DirectoryIterator, DirectoryOperator, PathResolver
};
use FireHub\Foundation\IO\Stream\ {
    Copier, File, Writer
};
use FireHub\Foundation\Temporal\ {
    DateTime, UnixTimestamp
};
use FireHub\Core\Exception\FireHubException;
use FireHub\Foundation\IO\Storage\Exception\MetadataException;
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
final readonly class Local implements Readable, Appendable, Locatable, Copyable, Movable, Listable,
    DirectoryWritable, DirectoryCopyable, DirectoryMovable, MetadataAware {

    /**
     * ### Storage path resolver
     * @since 1.0.0
     */
    private PathResolver $resolver;

    /**
     * ### Storage directory iterator
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\IO\Storage\Internal\DirectoryIterator
     */
    private DirectoryIterator $iterator;

    /**
     * ### Storage directory operator
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator
     */
    private DirectoryOperator $directory_operator;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param non-empty-string $root <p>
     * Existing local filesystem directory used as the storage root.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Permissions $permissions [optional] <p>
     * Filesystem permission policy.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidRootDirectoryException If the root directory is invalid.
     * @throws \FireHub\Runtime\Exception\PathResolutionException If we couldn't get an absolute path for a path or
     * file doesn't exist, or a script doesn't have executable permissions.
     *
     * @return void
     */
    public function __construct (
        string $root,
        private Permissions $permissions = new Permissions
    ) {

        $this->resolver = new PathResolver($root);

        $this->iterator = new DirectoryIterator($this->resolver);

        $this->directory_operator = new DirectoryOperator(
            $this->resolver,
            $this->permissions
        );

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
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
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
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
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
     * Creates a new stored object or replaces its existing contents through a temporary file.
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
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::write() To replace the destination contents.
     * @uses \FireHub\Foundation\IO\Stream\Writer::write() To write the contents to the temporary file.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To resolve the destination path.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     * @param string $contents <p>
     * Contents to store.
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
     * @return void
     */
    public function put (Path $path, string $contents):void {

        AtomicWriter::write(
            $this->resolver->resolveFile($path),
            static function (WritableStream $stream) use ($contents):void {

                Writer::write($stream, $contents);

            },
            $this->permissions
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
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To resolve the physical filesystem
     * path.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear the filesystem metadata cache.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To determine whether the resolved path is a regular file.
     * @uses \FireHub\Runtime\FileSystem\File::delete() To delete the stored object.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
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
     * @return void
     */
    public function delete (Path $path):void {

        $resolved = $this->resolver->resolveFile($path);

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
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
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
     * Copies the remaining source stream contents into a temporary file before replacing the destination object.
     *
     * The source stream remains owned by the caller.
     *
     * <code>
     * $source = $driver->readStream(
     *     new \FireHub\Foundation\IO\Storage\Path('source.txt')
     * );
     *
     * try {
     *
     *     $driver->writeStream(
     *         new \FireHub\Foundation\IO\Storage\Path('backup.txt'),
     *         $source
     *     );
     *
     * } finally {
     *
     *     $source->close();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To resolve the destination path.
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::write() To replace the destination contents.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copy() To transfer the source stream contents.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical destination path.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Source stream containing the contents to store.
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
     * @return void
     */
    public function writeStream (Path $path, ReadableStream $stream):void {

        AtomicWriter::write(
            $this->resolver->resolveFile($path),
            static function (WritableStream $destination) use ($stream):void {

                Copier::copy($stream, $destination);

            },
            $this->permissions
        );

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
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     */
    public function locate (Path $path):string {

        return $this->resolver->locate($path);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver->append(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt'),
     *     'Additional contents'
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To resolve the physical file path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Local::put() To create a missing stored file.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To invalidate cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To determine whether the file exists.
     * @uses \FireHub\Runtime\FileSystem\File::putContent() To append contents to an existing file.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored file.
     * </p>
     * @param string $contents <p>
     * Contents to append.
     * </p>.
     *
     * @throws \FireHub\Runtime\Exception\FileNotFoundException If $path is not file.
     * @throws \FireHub\Runtime\Exception\FileWriteException If the $create_file option is off or couldn't put
     * content on a path.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     *
     * @note Appending to an existing file is performed in place.
     * A failed write may leave partially appended contents.
     */
    public function append (Path $path, string $contents):void {

        $resolved = $this->resolver->resolveFile($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        if (!Runtime\FileSystem\File::isFile($resolved)) {

            $this->put($path, $contents);

            return;

        }

        Runtime\FileSystem\File::putContent(
            $resolved,
            $contents,
            true,
            create_file: false
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver->prepend(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt'),
     *     'Header'
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To resolve the physical destination
     * path.
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::write() To replace the stored file through a
     * temporary file.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Local::put() To create a missing stored file.
     * @uses \FireHub\Foundation\IO\Stream\File::close() To close the source file stream.
     * @uses \FireHub\Foundation\IO\Stream\Writer::write() To write the prepended contents.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copy() To copy the existing file contents.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To invalidate cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To determine whether the file exists.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored file.
     * </p>
     * @param string $contents <p>
     * Contents to prepend.
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
     * @return void
     */
    public function prepend (Path $path, string $contents):void {

        $resolved = $this->resolver->resolveFile($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        if (!Runtime\FileSystem\File::isFile($resolved)) {

            $this->put($path, $contents);

            return;

        }

        AtomicWriter::write(
            $resolved,
            static function (WritableStream $destination) use ($resolved, $contents):void {

                $source = new File(
                    $resolved,
                    Runtime\Stream\Enum\OpenMode::READ_BINARY
                );

                try {

                    Writer::write($destination, $contents);

                    Copier::copy($source, $destination);

                } finally {

                    $source->close();

                }

            },
            $this->permissions
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver->appendStream(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt'),
     *     $stream
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To resolve the physical destination
     * path.
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::write() To replace the stored file through a
     * temporary file.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Local::writeStream() To create a missing stored file.
     * @uses \FireHub\Foundation\IO\Stream\File::close() To close the internally created source stream.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copy() To copy existing and appended stream contents.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To invalidate cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To determine whether the file exists.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored file.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Source stream containing the contents to append.
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
     * @return void
     */
    public function appendStream (Path $path, ReadableStream $stream):void {

        $resolved = $this->resolver->resolveFile($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        if (!Runtime\FileSystem\File::isFile($resolved)) {

            $this->writeStream($path, $stream);

            return;

        }

        AtomicWriter::write(
            $resolved,
            static function (WritableStream $destination) use ($resolved, $stream):void {

                $source = new File(
                    $resolved,
                    Runtime\Stream\Enum\OpenMode::READ_BINARY
                );

                try {

                    Copier::copy($source, $destination);

                } finally {

                    $source->close();

                }

                Copier::copy($stream, $destination);

            },
            $this->permissions
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver->copy(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt'),
     *     new \FireHub\Foundation\IO\Storage\Path('backup.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locate() To resolve the existing source file.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To resolve the destination file path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Local::sameLocation() To determine whether both paths identify the
     * same filesystem location.
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::write() To replace the destination file through a
     * temporary file.
     * @uses \FireHub\Foundation\IO\Stream\File::close() To release the source file stream.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copy() To copy the source file contents.
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
     * @return void
     */
    public function copy (Path $source, Path $destination):void {

        $resolved_source = $this->resolver->locate($source);
        $resolved_destination = $this->resolver->resolveFile($destination);

        if ($this->sameLocation($resolved_source, $resolved_destination))
            return;

        AtomicWriter::write(
            $resolved_destination,
            static function (WritableStream $stream) use ($resolved_source):void {

                $file = new File($resolved_source, Runtime\Stream\Enum\OpenMode::READ_BINARY);

                try {

                    Copier::copy($file, $stream);

                } finally {

                    $file->close();

                }

            },
            $this->permissions
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver->move(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt'),
     *     new \FireHub\Foundation\IO\Storage\Path('archive/example.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locate() To resolve the existing source file.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To resolve the destination file path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Local::sameLocation() To determine whether both paths identify the
     * same filesystem location.
     * @uses \FireHub\Runtime\FileSystem\File::rename() To move the source file to the destination location.
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
     * @return void
     */
    public function move (Path $source, Path $destination):void {

        $resolved_source = $this->resolver->locate($source);
        $resolved_destination = $this->resolver->resolveFile($destination);

        if ($this->sameLocation($resolved_source, $resolved_destination))
            return;

        Runtime\FileSystem\File::rename($resolved_source, $resolved_destination);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * foreach ($driver->files(
     *     new \FireHub\Foundation\IO\Storage\Path('documents'),
     *     recursive: true
     * ) as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryIterator::files() To lazily enumerate stored files.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the starting directory is not a
     * directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function files (?Path $directory = null, bool $recursive = false):iterable {

        return $this->iterator->files($directory, $recursive);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * foreach ($driver->directories(
     *     new \FireHub\Foundation\IO\Storage\Path('documents'),
     *     recursive: true
     * ) as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryIterator::directories() To lazily enumerate storage
     * directories.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the starting directory is not a
     * directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function directories (?Path $directory = null, bool $recursive = false):iterable {

        return $this->iterator->directories($directory, $recursive);

    }

    /**
     * ### Determines whether a storage directory exists
     *
     * If no directory is specified, the storage root directory is inspected. A path identifying a regular file
     * produces false.
     *
     * <code>
     * $driver->directoryExists(new Path('documents'));
     *
     * $driver->directoryExists();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::exists() To determine whether the physical
     * directory exists.
     *
     * @param null|\FireHub\Core\Boundary\IO\Storage\Path $directory [optional] <p>
     * Logical directory path or null to inspect the storage root.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return bool True if the specified storage directory exists.
     */
    public function directoryExists (?Path $directory = null):bool {

        return $this->directory_operator->exists($directory);

    }

    /**
     * ### Creates a storage directory
     *
     * Creating an existing directory has no effect. Recursive creation includes all missing parent directories.
     *
     * Newly created directories use the configured storage directory permission policy.
     *
     * <code>
     * $driver->makeDirectory(new Path('documents'));
     *
     * $driver->makeDirectory(
     *     new Path('documents/archive/2026'),
     *     recursive: true
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::create() To create the physical storage
     * directory.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical path of the directory to create.
     * </p>
     * @param bool $recursive [optional] <p>
     * Whether missing parent directories should also be created.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\DirectoryNotFoundException If the parent directory does not
     * exist and recursive creation is not allowed.
     * @throws \FireHub\Runtime\Exception\CreateFolderException If we couldn't create a folder.
     * @throws \FireHub\Runtime\Exception\PathPermissionsException If failed to set permissions for a path.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    public function makeDirectory (Path $directory, bool $recursive = false):void {

        $this->directory_operator->create($directory, $recursive);

    }

    /**
     * ### Deletes a storage directory
     *
     * Deleting a nonexistent directory has no effect. If recursive deletion is disabled, the directory must be empty.
     *
     * Recursive deletion removes all supported descendant filesystem objects and directories.
     *
     * Recursive deletion is not guaranteed to be atomic.
     *
     * <code>
     * $driver->deleteDirectory(new Path('empty'));
     *
     * $driver->deleteDirectory(
     *     new Path('documents/archive'),
     *     recursive: true
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::delete() To delete the physical storage
     * directory.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical path of the directory to delete.
     * </p>
     * @param bool $recursive [optional] <p>
     * Whether descendant objects and directories should also be deleted.
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
     * @return void
     */
    public function deleteDirectory (Path $directory, bool $recursive = false):void {

        $this->directory_operator->delete($directory, $recursive);

    }

    /**
     * ### Copies a storage directory
     *
     * Recursively copies the source directory and its descendant regular files and directories to a new destination.
     *
     * Empty directories are preserved. The destination parent directory must already exist. Copying an existing
     * directory to itself has no effect.
     *
     * Newly created filesystem objects use the configured storage permission policy.
     *
     * Directory copying is not guaranteed to be atomic.
     *
     * <code>
     * $driver->copyDirectory(
     *     new Path('documents'),
     *     new Path('backup')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::copy() To recursively copy the physical
     * storage directory.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $source <p>
     * Logical source directory path.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Storage\Path $destination <p>
     * Logical destination directory path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    public function copyDirectory (Path $source, Path $destination):void {

        $this->directory_operator->copy($source, $destination);

    }

    /**
     * ### Moves a storage directory
     *
     * Moves the source directory and its descendant filesystem objects to a new destination using native filesystem
     * relocation.
     *
     * The destination parent directory must already exist. Moving an existing directory to itself has no effect.
     *
     * Existing filesystem permissions are not explicitly modified.
     *
     * <code>
     * $driver->moveDirectory(
     *     new Path('documents/archive'),
     *     new Path('backup/archive')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::move() To relocate the physical storage
     * directory.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $source <p>
     * Logical source directory path.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Storage\Path $destination <p>
     * Logical destination directory path.
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
     * @return void
     */
    public function moveDirectory (Path $source, Path $destination):void {

        $this->directory_operator->move($source, $destination);

    }

    /**
     * ### Retrieves stored object metadata
     *
     * Retrieves a snapshot of basic filesystem metadata associated with the specified stored regular object.
     *
     * The last modification time is represented in UTC.
     *
     * <code>
     * $metadata = $driver->metadata(new Path('documents/example.txt'));
     *
     * echo $metadata->path()->value();
     * echo $metadata->size();
     * echo $metadata->lastModified()?->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locate() To resolve the physical path of the
     * stored object.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To invalidate cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\Metadata::statistics() To retrieve filesystem statistics.
     * @uses \FireHub\Foundation\Temporal\UnixTimestamp To represent the stored object last modification timestamp.
     * @uses \FireHub\Foundation\Temporal\DateTime::fromTimestamp() To convert the last modification timestamp into a
     * date and time value.
     * @uses \FireHub\Foundation\IO\Storage\Metadata To represent the stored object metadata snapshot.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
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
     * @return \FireHub\Foundation\IO\Storage\Metadata Stored object metadata.
     */
    public function metadata (Path $path):Metadata {

        $physical = $this->resolver->locate($path);

        Runtime\FileSystem\Metadata::clearCache(true, $physical);

        try {

            $statistics = Runtime\FileSystem\Metadata::statistics($physical);

            return new Metadata(
                $path,
                $statistics['size'],
                DateTime::fromTimestamp(
                    new UnixTimestamp($statistics['mtime'], 0)
                )
            );

        } catch (FireHubException $exception) {

            throw new MetadataException(
                context: [
                    'path' => $path->value()
                ],
                previous: $exception
            );

        }

    }

    /**
     * ### Determines whether two filesystem paths identify the same location
     *
     * Compares resolved filesystem paths.
     *
     * Windows paths are compared using an ASCII case-insensitive comparison. Other filesystem paths are compared
     * using a case-sensitive comparison.
     *
     * This comparison does not guarantee physical file identity when filesystem aliases or hard links are involved.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\PlatformInfo::OS_FAMILY To determine the operating system family.
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To compare paths lexically.
     *
     * @param non-empty-string $source <p>
     * Resolved physical source file path.
     * </p>
     * @param non-empty-string $destination <p>
     * Resolved physical destination file path.
     * </p>
     *
     * @return bool True if both paths identify the same filesystem location according to the configured comparison
     * strategy.
     */
    private function sameLocation (string $source, string $destination):bool {

        return PlatformInfo::OS_FAMILY === 'Windows'
            ? Runtime\Str\SB\Compare::lexical($source, $destination, false) === 0
            : $source === $destination;

    }

}