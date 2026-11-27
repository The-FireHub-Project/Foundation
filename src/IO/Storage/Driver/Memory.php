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

use FireHub\Core\Boundary\IO\Storage\Path as PathContract;
use FireHub\Core\Boundary\IO\Storage\Driver\ {
    Readable, Writable
};
use FireHub\Core\Boundary\IO\Stream\Readable as ReadableStream;
use FireHub\Foundation\IO\Storage\Path;
use FireHub\Foundation\IO\Stream\ {
    Copier, Memory as MemoryStream, Writer
};
use FireHub\Foundation\IO\Storage\Exception\ObjectNotFoundException;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;
use Throwable;

/**
 * ### In-memory storage driver
 *
 * Provides non-persistent storage of objects within the memory of the current PHP process.
 *
 * Each driver instance maintains an independent collection of stored objects.
 *
 * Supports direct content retrieval, content storage, stream-based access, and object deletion.
 *
 * Stored objects do not have physical filesystem locations.
 *
 * <code>
 * $driver = new Memory;
 *
 * $driver->put(
 *     new \FireHub\Foundation\IO\Storage\Path('example.txt'),
 *     'Contents'
 * );
 * </code>
 *
 * @since 1.0.0
 */
final class Memory implements Readable, Writable {

    /**
     * ### Stored objects
     *
     * Maps logical storage paths to their corresponding contents.
     * @since 1.0.0
     *
     * @var array<string, string>
     */
    private array $objects = [];

    /**
     * ### Determines whether a stored object exists
     *
     * <code>
     * $driver->exists(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the storage key.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return bool True if the stored object exists, false otherwise.
     */
    public function exists (PathContract $path):bool {

        return isset($this->objects[$this->key($path)]);

    }

    /**
     * ### Gets the contents of a stored object
     *
     * Returns the complete contents of an existing stored object, including empty or binary content.
     *
     * <code>
     * $contents = $driver->get(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the storage key.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\ObjectNotFoundException If the stored object does not exist.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return string Complete contents of the stored object.
     */
    public function get (PathContract $path):string {

        return $this->objects[$this->key($path)]
            ?? throw new ObjectNotFoundException;

    }

    /**
     * ### Stores the contents of an object
     *
     * Creates a new stored object or replaces the contents of an existing one.
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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the storage key.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     * @param string $contents <p>
     * Contents to store.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function put (PathContract $path, string $contents):void {

        $this->objects[$this->key($path)] = $contents;

    }

    /**
     * ### Deletes a stored object
     *
     * This operation has no effect if the stored object does not exist.
     *
     * <code>
     * $driver->delete(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the storage key.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function delete (PathContract $path):void {

        unset($this->objects[$this->key($path)]);

    }

    /**
     * ### Opens a readable stream for a stored object
     *
     * Creates an independent memory stream containing a snapshot of the stored object.
     *
     * Modifying the returned stream does not modify the stored object.
     *
     * The caller is responsible for closing the returned stream.
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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::get() To retrieve the stored contents.
     * @uses \FireHub\Foundation\IO\Stream\Memory::rewind() To reset the stream position.
     * @uses \FireHub\Foundation\IO\Stream\Writer::write() To write the stored contents to the stream.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::close() To release the temporary stream.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To check the length of the returned contents.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ObjectNotFoundException If the stored object does not exist.
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If writing fails.
     *
     * @return \FireHub\Core\Boundary\IO\Stream\Readable Readable stream positioned at the beginning of the object.
     */
    public function readStream (PathContract $path):ReadableStream {

        $contents = $this->get($path);

        $stream = new MemoryStream;

        try {

            Writer::write($stream, $contents);

            $stream->rewind();

            return $stream;

        } catch (Throwable $exception) {

            $stream->close();

            throw $exception;

        }

    }

    /**
     * ### Stores the contents of a readable stream
     *
     * Copies the remaining contents of the source stream into memory.
     *
     * The stored object is replaced only after the copy operation has completed successfully.
     *
     * The source stream remains owned by the caller.
     *
     * <code>
     * $source = $driver->readStream(
     *     new \FireHub\Foundation\IO\Storage\Path('example.txt')
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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the storage key.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copy() To copy the source stream.
     * @uses \FireHub\Foundation\IO\Stream\Memory::rewind() To reset the destination stream position.
     * @uses \FireHub\Foundation\IO\Stream\Memory::contents() To retrieve the copied contents.
     * @uses \FireHub\Foundation\IO\Stream\Memory::close() To release the temporary stream.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical destination path.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Source stream containing the contents to store.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function writeStream (PathContract $path, ReadableStream $stream):void {

        $key = $this->key($path);

        $destination = new MemoryStream;

        try {

            Copier::copy($stream, $destination);

            $destination->rewind();

            $contents = $destination->contents();

        } finally {

            $destination->close();

        }

        $this->objects[$key] = $contents;

    }

    /**
     * ### Resolves an internal storage key
     *
     * Validates externally supplied path implementations and converts logical paths into internal storage keys.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Storage\Path::value() To retrieve the logical path value.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical storage path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return non-empty-string Internal storage key.
     */
    private function key (PathContract $path):string {

        $path = $path instanceof Path
            ? $path
            : new Path($path->value());

        return 'storage:'.$path->value();

    }

}