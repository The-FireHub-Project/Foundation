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
use FireHub\Foundation\IO\Storage\Exception\UnsupportedOperationException;

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
            : throw new UnsupportedOperationException;

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
            : throw new UnsupportedOperationException;

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
            : throw new UnsupportedOperationException;

    }

}