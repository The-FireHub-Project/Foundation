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

namespace FireHub\Foundation\IO\Storage;

use FireHub\Core\Boundary\IO\Storage\ {
    Disk as DiskContract, Driver, Path as PathContract
};
use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\IO\Storage\Exception\ {
    ObjectNotFoundException, UnsupportedOperationException
};

/**
 * ### Storage disk
 *
 * Represents a configured storage resource that provides a unified interface for accessing and managing stored
 * objects through an underlying storage driver.
 *
 * The disk is responsible for logical path normalization, capability detection, and delegation of storage operations.
 *
 * <code>
 * $disk = new Disk(new Local(__DIR__.'/storage'));
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Disk implements DiskContract {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Driver $driver <p>
     * Underlying storage driver.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Driver $driver
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isReadable ():bool {

        return $this->driver instanceof Driver\Readable;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isWritable ():bool {

        return $this->driver instanceof Driver\Writable;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isLocatable ():bool {

        return $this->driver instanceof Driver\Locatable;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->exists('documents/report.pdf');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To resolve the logical storage path.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver::exists() To determine whether the stored object exists.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function exists (string|PathContract $path):bool {

        return $this->driver->exists($this->path($path));

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->missing('documents/report.pdf');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::exists() To determine whether the stored object exists.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function missing (string|PathContract $path):bool {

        return !$this->exists($path);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $contents = $disk->get('documents/report.pdf');
     * </code>
     *
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support reading.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function get (string|PathContract $path):string {

        return $this->readable()->get($this->path($path));

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->put('example.txt', 'Contents');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To resolve the logical storage path.
     * @uses \FireHub\Foundation\IO\Storage\Disk::writable() To resolve the writable storage driver.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Writable::put() To write the contents to the storage.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support writing.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function put (string|PathContract $path, string $contents):void {

        $this->writable()->put($this->path($path), $contents);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->delete('example.txt');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To resolve the logical storage path.
     * @uses \FireHub\Foundation\IO\Storage\Disk::writable() To resolve the writable storage driver.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Writable::delete() To delete the stored object.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support deletion.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function delete (string|PathContract $path):void {

        $this->writable()->delete($this->path($path));

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $stream = $disk->readStream('example.txt');
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
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To resolve the logical storage path.
     * @uses \FireHub\Foundation\IO\Storage\Disk::readable() To resolve the readable storage driver.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::readStream() To read the stored object as a stream.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support reading.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function readStream (string|PathContract $path):Readable {

        return $this->readable()->readStream($this->path($path));

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $stream = $source->readStream('example.txt');
     *
     * try {
     *     $disk->writeStream('backup/example.txt', $stream);
     * } finally {
     *     $stream->close();
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To resolve the logical storage path.
     * @uses \FireHub\Foundation\IO\Storage\Disk::writable() To resolve the writable storage driver.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::writeStream() To write the stream to the storage.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support writing.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function writeStream (string|PathContract $path, Readable $stream):void {

        $this->writable()->writeStream($this->path($path), $stream);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $path = $disk->locate('templates/home.php');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To resolve the logical storage path.
     * @uses \FireHub\Foundation\IO\Storage\Disk::locatable() To resolve the locatable storage driver.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Locatable::locate() To resolve the physical path.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support physical path resolution.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function locate (string|PathContract $path):string {

        return $this->locatable()->locate($this->path($path));

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->append('example.txt', 'Additional contents');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::appendable() To retrieve the appendable storage driver.
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To normalize the logical storage path.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Appendable::append() To append the supplied contents.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the configured driver is not
     * appendable.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function append (string|PathContract $path, string $contents):void {

        $this->appendable()->append(
            $this->path($path),
            $contents
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->prepend('example.txt', 'Header');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::appendable() To retrieve the appendable storage driver.
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To normalize the logical storage path.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Appendable::prepend() To prepend the supplied contents.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the driver does not support
     * prepend operations.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function prepend (string|PathContract $path, string $contents):void {

        $this->appendable()->prepend(
            $this->path($path),
            $contents
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->appendStream('example.txt', $stream);
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::appendable() To retrieve the appendable storage driver.
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To normalize the logical storage path.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Appendable::appendStream() To append the source stream contents.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the driver does not support
     * append operations.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function appendStream (string|PathContract $path, Readable $stream):void {

        $this->appendable()->appendStream(
            $this->path($path),
            $stream
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->isAppendable();
     * // true
     * </code>
     *
     * @since 1.0.0
     */
    public function isAppendable ():bool {

        return $this->driver instanceof Driver\Appendable;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->copy('example.txt', 'backup.txt');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::isCopyable() To determine whether copying is supported.
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To normalize the logical storage paths.
     * @uses \FireHub\Foundation\IO\Storage\Disk::sameObject() To determine whether both paths identify the same
     * logical object.
     * @uses \FireHub\Foundation\IO\Storage\Disk::readStream() To open the source object for reading.
     * @uses \FireHub\Foundation\IO\Storage\Disk::writeStream() To write the source contents to the destination object.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Copyable::copy() To perform a native copy operation.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::close() To release the source stream.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If copying is not supported.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function copy (string|PathContract $source, string|PathContract $destination):void {

        if (!$this->isCopyable())
            throw new UnsupportedOperationException(
                context: [
                    'operation' => 'copy'
                ]
            );

        $source = $this->path($source);
        $destination = $this->path($destination);

        if ($this->sameObject($source, $destination))
            return;

        if ($this->driver instanceof Driver\Copyable) {

            $this->driver->copy($source, $destination);

            return;

        }

        $stream = $this->readStream($source);

        try {

            $this->writeStream($destination, $stream);

        } finally {

            $stream->close();

        }

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $disk->move('example.txt', 'archive/example.txt');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::isMovable() To determine whether moving is supported.
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To normalize the logical storage paths.
     * @uses \FireHub\Foundation\IO\Storage\Disk::sameObject() To determine whether both paths identify the same
     * logical object.
     * @uses \FireHub\Foundation\IO\Storage\Disk::copy() To copy the source object when native moving is unavailable.
     * @uses \FireHub\Foundation\IO\Storage\Disk::delete() To delete the source object after a successful copy.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Movable::move() To perform a native move operation.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If moving is not supported.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function move (string|PathContract $source, string|PathContract $destination):void {

        if (!$this->isMovable())
            throw new UnsupportedOperationException(
                context: [
                    'operation' => 'move'
                ]
            );

        $source = $this->path($source);
        $destination = $this->path($destination);

        if ($this->sameObject($source, $destination))
            return;

        if ($this->driver instanceof Driver\Movable) {

            $this->driver->move($source, $destination);

            return;

        }

        $this->copy($source, $destination);

        $this->delete($source);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * if ($disk->isCopyable()) {
     *
     *     $disk->copy('example.txt', 'backup.txt');
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::isReadable() To determine whether the storage driver supports reading.
     * @uses \FireHub\Foundation\IO\Storage\Disk::isWritable() To determine whether the storage driver supports writing.
     *
     * @return bool True if copying stored objects is supported.
     */
    public function isCopyable ():bool {

        return $this->driver instanceof Driver\Copyable
            || ($this->isReadable() && $this->isWritable());

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * if ($disk->isMovable()) {
     *
     *     $disk->move('example.txt', 'archive/example.txt');
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::isCopyable() To determine whether the disk supports fallback moving.
     *
     * @return bool True if moving stored objects is supported.
     */
    public function isMovable ():bool {

        return $this->driver instanceof Driver\Movable
            || $this->isCopyable();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * foreach ($disk->files('documents') as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::listable() To retrieve the listable storage driver.
     * @uses \FireHub\Foundation\IO\Storage\Disk::directoryPath() To normalize the logical directory path.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Listable::files() To list stored objects.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function files (null|string|PathContract $directory = null):iterable {

        return $this->listable()->files(
            $this->directoryPath($directory)
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * foreach ($disk->allFiles('documents') as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::listable() To retrieve the listable storage driver.
     * @uses \FireHub\Foundation\IO\Storage\Disk::directoryPath() To normalize the logical directory path.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Listable::files() To recursively list stored objects.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function allFiles (null|string|PathContract $directory = null):iterable {

        return $this->listable()->files(
            $this->directoryPath($directory),
            true
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * foreach ($disk->directories('documents') as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::listable() To retrieve the listable storage driver.
     * @uses \FireHub\Foundation\IO\Storage\Disk::directoryPath() To normalize the logical directory path.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Listable::directories() To list storage directories.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function directories (null|string|PathContract $directory = null):iterable {

        return $this->listable()->directories(
            $this->directoryPath($directory)
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * foreach ($disk->allDirectories('documents') as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::listable() To retrieve the listable storage driver.
     * @uses \FireHub\Foundation\IO\Storage\Disk::directoryPath() To normalize the logical directory path.
     * @uses \FireHub\Core\Boundary\IO\Storage\Driver\Listable::directories() To recursively list storage directories.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function allDirectories (null|string|PathContract $directory = null):iterable {

        return $this->listable()->directories(
            $this->directoryPath($directory),
            true
        );

    }

    /**
     *{@inheritDoc}
     *
     * <code>
     * $disk->isListable();
     * // true
     * </code>
     *
     * @since 1.0.0
     */
    public function isListable ():bool {

        return $this->driver instanceof Driver\Listable;

    }

    /**
     * ### Resolves a logical storage path
     * @since 1.0.0
     *
     * @param non-empty-string|\FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of a stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Foundation\IO\Storage\Path Validated logical storage path.
     */
    private function path (string|PathContract $path):Path {

        return $path instanceof Path
            ? $path
            : new Path($path instanceof PathContract ? $path->value() : $path);

    }

    /**
     * ### Resolves the readable storage driver
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support reading.
     *
     * @return \FireHub\Core\Boundary\IO\Storage\Driver\Readable Readable storage driver.
     */
    private function readable ():Driver\Readable {

        return $this->driver instanceof Driver\Readable
            ? $this->driver
            : throw new UnsupportedOperationException(
                context: [
                    'operation' => 'read'
                ]
            );

    }

    /**
     * ### Resolves the writable storage driver
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support writing.
     *
     * @return \FireHub\Core\Boundary\IO\Storage\Driver\Writable Writable storage driver.
     */
    private function writable ():Driver\Writable {

        return $this->driver instanceof Driver\Writable
            ? $this->driver
            : throw new UnsupportedOperationException(
                context: [
                    'operation' => 'write'
                ]
            );

    }

    /**
     * ### Resolves the locatable storage driver
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the underlying storage
     * driver does not support physical path resolution.
     *
     * @return \FireHub\Core\Boundary\IO\Storage\Driver\Locatable Locatable storage driver.
     */
    private function locatable ():Driver\Locatable {

        return $this->driver instanceof Driver\Locatable
            ? $this->driver
            : throw new UnsupportedOperationException(
                context: [
                    'operation' => 'locate'
                ]
            );

    }

    /**
     * ### Gets the appendable storage driver
     *
     * Ensures that the configured storage driver supports extending stored object contents.
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the configured driver is not
     * appendable.
     *
     * @return \FireHub\Core\Boundary\IO\Storage\Driver\Appendable Appendable storage driver.
     */
    private function appendable ():Driver\Appendable {

        return $this->driver instanceof Driver\Appendable
            ? $this->driver
            : throw new UnsupportedOperationException(
                context: [
                    'operation' => 'append'
                ]
            );

    }

    /**
     * ### Gets the listable storage driver
     *
     * Ensures that the configured storage driver supports listing stored objects and directories.
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException If the configured storage
     * driver is not listable.
     *
     * @return \FireHub\Core\Boundary\IO\Storage\Driver\Listable Listable storage driver.
     */
    private function listable ():Driver\Listable {

        return $this->driver instanceof Driver\Listable
            ? $this->driver
            : throw new UnsupportedOperationException(
                context: [
                    'operation' => 'list'
                ]
            );

    }

    /**
     * ### Determines whether two paths identify the same logical object
     *
     * Compares normalized logical storage paths.
     *
     * If both paths are identical, the source object must exist before the operation can be treated as a no-op.
     *
     * This method does not determine whether different logical paths refer to the same physical filesystem object.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Path::value() To retrieve the normalized logical path.
     * @uses \FireHub\Foundation\IO\Storage\Disk::exists() To determine whether the source object exists.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $source <p>
     * Normalized logical source path.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Path $destination <p>
     * Normalized logical destination path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return bool True if both logical storage paths are identical.
     */
    private function sameObject (Path $source, Path $destination):bool {

        if ($source->value() !== $destination->value())
            return false;

        if (!$this->exists($source))
            throw new ObjectNotFoundException(
                context: [
                    'path' => $source->value()
                ]
            );

        return true;

    }

    /**
     * ### Normalizes a logical storage directory path
     *
     * Preserves null as the storage root directory identifier. Non-null paths are normalized through the standard
     * storage path normalization process.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Disk::path() To normalize a non-null logical storage path.
     *
     * @param null|non-empty-string|\FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical directory path or null to identify the storage root.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Foundation\IO\Storage\Path Normalized logical directory path or null for the storage root.
     */
    private function directoryPath (null|string|PathContract $directory):?Path {

        return $directory === null
            ? null
            : $this->path($directory);

    }

}