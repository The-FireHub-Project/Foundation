<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.3
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Storage\Driver;

use FireHub\Core\Boundary\IO\Storage\Path as PathContract;
use FireHub\Core\Boundary\IO\Storage\Driver\ {
    Appendable, Copyable, Listable, Movable, Readable
};
use FireHub\Core\Boundary\IO\Stream\Readable as ReadableStream;
use FireHub\Foundation\IO\Storage\Path;
use FireHub\Foundation\IO\Stream\ {
    Copier, Memory as MemoryStream, Writer
};
use FireHub\Foundation\IO\Storage\Exception\ {
    InvalidObjectTypeException, ObjectNotFoundException
};
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
final class Memory implements Readable, Appendable, Copyable, Movable, Listable {

    /**
     * ### Internal storage key prefix
     *
     * Prevents PHP from converting numeric logical paths into integer keys.
     * @since 1.0.0
     *
     * @var non-empty-string
     */
    private const string KEY_PREFIX = 'storage:';

    /**
     * ### Stored objects
     *
     * Maps logical storage paths to their corresponding contents.
     * @since 1.0.0
     *
     * @var array<non-empty-string, string>
     */
    private array $objects = [];

    /**
     * ### Registered storage directories
     *
     * Stores explicitly created logical directories. The storage root directory exists implicitly.
     * @since 1.0.0
     *
     * @var array<non-empty-string, true>
     */
    private array $directories = [];

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
     * {@inheritDoc}
     *
     * <code>
     * $driver = new Memory;
     * $path = new \FireHub\Foundation\IO\Storage\Path('example.txt');
     *
     * $driver->put($path, 'FireHub');
     * $driver->append($path, ' Project');
     *
     * $driver->get($path);
     * // 'FireHub Project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the internal storage key.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     * @param string $contents <p>
     * Contents to append.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function append (PathContract $path, string $contents):void {

        $key = $this->key($path);

        $this->objects[$key] = ($this->objects[$key] ?? '').$contents;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver = new Memory;
     * $path = new \FireHub\Foundation\IO\Storage\Path('example.txt');
     *
     * $driver->put($path, 'FireHub Project');
     * $driver->prepend($path, 'The ');
     *
     * $driver->get($path);
     * // 'The FireHub Project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the internal storage key.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     * @param string $contents <p>
     * Contents to prepend.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function prepend (PathContract $path, string $contents):void {

        $key = $this->key($path);

        $this->objects[$key] = $contents.($this->objects[$key] ?? '');

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver = new Memory;
     * $path = new \FireHub\Foundation\IO\Storage\Path('example.txt');
     *
     * $driver->put($path, 'FireHub');
     *
     * $stream = new \FireHub\Foundation\IO\Stream\Memory;
     *
     * \FireHub\Foundation\IO\Stream\Writer::write($stream, ' Project');
     *
     * $stream->rewind();
     *
     * $driver->appendStream($path, $stream);
     *
     * $stream->close();
     *
     * $driver->get($path);
     * // 'FireHub Project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the internal storage key.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copy() To copy the source stream contents.
     * @uses \FireHub\Foundation\IO\Stream\Memory::rewind() To reset the temporary stream position.
     * @uses \FireHub\Foundation\IO\Stream\Memory::contents() To retrieve the copied contents.
     * @uses \FireHub\Foundation\IO\Stream\Memory::close() To release the temporary stream.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Source stream containing the contents to append.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function appendStream (PathContract $path, ReadableStream $stream):void {

        $key = $this->key($path);

        $destination = new MemoryStream;

        try {

            Copier::copy($stream, $destination);

            $destination->rewind();

            $contents = $destination->contents();

        } finally {

            $destination->close();

        }

        $this->objects[$key] = ($this->objects[$key] ?? '').$contents;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver = new Memory;
     *
     * $source = new \FireHub\Foundation\IO\Storage\Path('example.txt');
     * $destination = new \FireHub\Foundation\IO\Storage\Path('backup.txt');
     *
     * $driver->put($source, 'The FireHub Project');
     * $driver->copy($source, $destination);
     *
     * $driver->get($destination);
     * // 'The FireHub Project'
     *
     * $driver->exists($source);
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function copy (PathContract $source, PathContract $destination):void {

        $source_key = $this->key($source);
        $destination_key = $this->key($destination);

        if (!isset($this->objects[$source_key]))
            throw new ObjectNotFoundException(
                context: [
                    'path' => $source->value()
                ]
            );

        if ($source_key === $destination_key)
            return;

        $this->objects[$destination_key] = $this->objects[$source_key];

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * $driver = new Memory;
     *
     * $source = new \FireHub\Foundation\IO\Storage\Path('example.txt');
     * $destination = new \FireHub\Foundation\IO\Storage\Path('archive/example.txt');
     *
     * $driver->put($source, 'The FireHub Project');
     * $driver->move($source, $destination);
     *
     * $driver->get($destination);
     * // 'The FireHub Project'
     *
     * $driver->exists($source);
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function move (PathContract $source, PathContract $destination):void {

        $source_key = $this->key($source);
        $destination_key = $this->key($destination);

        if (!isset($this->objects[$source_key]))
            throw new ObjectNotFoundException(
                context: [
                    'path' => $source->value()
                ]
            );

        if ($source_key === $destination_key)
            return;

        $this->objects[$destination_key] = $this->objects[$source_key];

        unset($this->objects[$source_key]);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * foreach ($driver->files(new Path('documents'), true) as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::listingPrefix() To resolve the internal directory prefix.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::matchesPrefix() To determine whether a stored object
     * belongs to the requested directory.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::pathFromKey() To reconstruct logical storage paths.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the specified directory path
     * identifies a stored object.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function files (?PathContract $directory = null, bool $recursive = false):iterable {

        $prefix = $this->listingPrefix($directory);

        foreach ($this->objects as $key => $contents)

            if ($this->matchesPrefix($key, $prefix, $recursive))
                yield $this->pathFromKey($key);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * foreach ($driver->directories(new Path('documents'), true) as $path) {
     *
     *     echo $path->value();
     *
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::listingPrefix() To resolve the internal directory prefix.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::matchesPrefix() To determine whether a directory belongs
     * to the requested directory.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::pathFromKey() To reconstruct logical storage paths.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the specified directory path
     * identifies a stored object.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function directories (?PathContract $directory = null, bool $recursive = false):iterable {

        $prefix = $this->listingPrefix($directory);

        foreach ($this->directories as $key => $exists)

            if ($this->matchesPrefix($key, $prefix, $recursive))
                yield $this->pathFromKey($key);

    }

    /**
     * ### Resolves an internal storage key
     *
     * Validates externally supplied path implementations and converts logical paths into internal storage keys.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Storage\Path::value() To retrieve the logical path value.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::KEY_PREFIX To prefix the internal storage key.
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

        return self::KEY_PREFIX.$path->value();

    }

    /**
     * ### Resolves an internal directory listing prefix
     *
     * Converts a logical directory path into an internal storage key prefix.
     *
     * The storage root is represented by the internal key prefix itself. Non-root directory prefixes include a
     * trailing path separator.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the internal storage key.
     *
     * @param null|\FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical directory path or null for the storage root.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the specified directory path
     * identifies a stored object.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return non-empty-string Internal directory listing prefix.
     */
    private function listingPrefix (?PathContract $directory):string {

        if ($directory === null)
            return self::KEY_PREFIX;

        $key = $this->key($directory);

        if (isset($this->objects[$key]))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $directory->value()
                ]
            );

        return $key.'/';

    }

    /**
     * ### Determines whether a storage key matches a directory listing prefix
     *
     * Matches storage keys belonging to the specified logical directory.
     *
     * Non-recursive matching excludes objects and directories located
     * within descendant directories.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To determine whether the storage key starts with the
     * directory prefix.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the directory prefix length.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To retrieve the relative portion of the storage key.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To determine whether the relative portion of the storage key
     * contains the directory separator.
     *
     * @param non-empty-string $key <p>
     * Internal storage key.
     * </p>
     * @param non-empty-string $prefix <p>
     * Internal directory listing prefix.
     * </p>
     * @param bool $recursive <p>
     * Whether descendant objects and directories should be included.
     * </p>
     *
     * @return bool True if the storage key matches the listing criteria.
     */
    private function matchesPrefix (string $key, string $prefix, bool $recursive):bool {

        if (!Runtime\Str\SB\Search::startsWith($prefix, $key))
            return false;

        if ($recursive)
            return true;

        $relative = Runtime\Str\SB\Access::part(
            $key,
            Runtime\Str\SB\Inspection::length($prefix)
        );

        return !Runtime\Str\SB\Search::contains('/', $relative);

    }

    /**
     * ### Reconstructs a logical storage path from an internal storage key
     *
     * Removes the internal storage key prefix and constructs the corresponding logical storage path.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the internal storage key prefix length.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To remove the internal storage key prefix.
     *
     * @param non-empty-string $key <p>
     * Internal storage key.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Foundation\IO\Storage\Path Logical storage path.
     */
    private function pathFromKey (string $key):Path {

        /** @var non-empty-string $value */
        $value = Runtime\Str\SB\Access::part(
            $key,
            Runtime\Str\SB\Inspection::length(self::KEY_PREFIX)
        );

        return new Path($value);

    }

}