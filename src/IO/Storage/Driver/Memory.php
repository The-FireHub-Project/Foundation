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
    Appendable, Copyable, Listable, Locatable, Movable, Readable,
    DirectoryCopyable, DirectoryMovable, DirectoryWritable
};
use FireHub\Core\Boundary\IO\Stream\Readable as ReadableStream;
use FireHub\Foundation\IO\Storage\Path;
use FireHub\Foundation\IO\Stream\ {
    Copier, Memory as MemoryStream, Writer
};
use FireHub\Foundation\IO\Storage\Exception\ {
    DirectoryAlreadyExistsException, DirectoryNotFoundException, DirectoryNotEmptyException,
    InvalidDirectoryDestinationException, InvalidObjectTypeException, ObjectNotFoundException
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
final class Memory implements Readable, Appendable, Copyable, Movable, Listable, DirectoryWritable,
    DirectoryCopyable, DirectoryMovable {

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::normalizePath() To normalize the storage path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateAncestors() To validate the ancestor directories.
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

        $path = $this->normalizePath($path);

        $this->validateAncestors($path);

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the object path.
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

        $path = $this->validateObjectPath($path);

        return $this->objects[$this->key($path)]
            ?? throw new ObjectNotFoundException(
                context: [
                    'path' => $path->value()
                ]
            );

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the object path.
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

        $key = $this->key($this->validateObjectPath($path));

        $this->objects[$key] = $contents;

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the object path.
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

        unset($this->objects[$this->key($this->validateObjectPath($path))]);

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
     * @throws \FireHub\Foundation\IO\Storage\Exception\ObjectNotFoundException If the stored object does not exist.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the object path.
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

        $key = $this->key($this->validateObjectPath($path));

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the object path.
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

        $key = $this->key($this->validateObjectPath($path));

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the object path.
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

        $key = $this->key($this->validateObjectPath($path));

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the object path.
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

        $key = $this->key($this->validateObjectPath($path));

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::put() To store the source object.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function copy (PathContract $source, PathContract $destination):void {

        $this->put($destination, $this->get($source));

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To resolve the internal storage key.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the object path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::get() To retrieve the source object.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateWritableObjectPath() To validate the destination path.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function move (PathContract $source, PathContract $destination):void {

        $source = $this->validateObjectPath($source);

        $contents = $this->get($source);

        $destination = $this->validateWritableObjectPath($destination);

        $source_key = $this->key($source);
        $destination_key = $this->key($destination);

        if ($source_key === $destination_key)
            return;

        $this->objects[$destination_key] = $contents;

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
     * ### Determines whether a storage directory exists
     *
     * Determines whether the specified logical path identifies an existing in-memory storage directory.
     *
     * The storage root directory always exists. A path identifying a stored regular object produces false.
     *
     * <code>
     * $driver->directoryExists(new Path('documents'));
     *
     * $driver->directoryExists();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key()
     * To retrieve the internal storage key.
     *
     * @param null|\FireHub\Core\Boundary\IO\Storage\Path $directory [optional] <p>
     * Logical directory path or null to inspect the storage root.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return bool True if the specified storage directory exists.
     */
    public function directoryExists (?PathContract $directory = null):bool {

        return $directory === null
            || isset($this->directories[$this->key($directory)]);

    }

    /**
     * ### Creates a storage directory
     *
     * Creates an in-memory directory at the specified logical storage path.
     *
     * Creating an existing directory has no effect. Recursive creation includes all missing parent directories.
     *
     * A directory cannot occupy the same logical path as a stored object.
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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::normalizePath() To normalize the logical directory path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve the internal storage key.
     * @uses \FireHub\Foundation\IO\Storage\Path::parent() To retrieve the logical parent directory.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::directoryExists() To determine whether the parent
     * directory exists.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::makeDirectory() To recursively create missing parent
     * directories.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical path of the directory to create.
     * </p>
     * @param bool $recursive [optional] <p>
     * Whether missing parent directories should also be created.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function makeDirectory (PathContract $directory, bool $recursive = false):void {

        $directory = $this->normalizePath($directory);
        $key = $this->key($directory);

        if (isset($this->directories[$key]))
            return;

        if (isset($this->objects[$key]))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $directory->value()
                ]
            );

        $parent = $directory->parent();

        if ($parent !== null && !$this->directoryExists($parent)) {

            if (isset($this->objects[$this->key($parent)]))
                throw new InvalidObjectTypeException(
                    context: [
                        'path' => $parent->value()
                    ]
                );

            if (!$recursive)
                throw new DirectoryNotFoundException(
                    context: [
                        'path' => $parent->value()
                    ]
                );

            $this->makeDirectory($parent, true);

        }

        $this->directories[$key] = true;

    }

    /**
     * ### Deletes a storage directory
     *
     * Removes the specified in-memory storage directory.
     *
     * Deleting a nonexistent directory has no effect. If recursive deletion is disabled, the directory must be empty.
     *
     * Recursive deletion removes all descendant-stored regular objects and directories.
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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::normalizePath() To normalize the logical directory path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateAncestors() To validate the logical directory path
     * hierarchy.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve the internal storage directory key.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::isDirectoryEmpty() To determine whether the directory
     * contains descendants.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::deleteDescendants() To remove descendant stored objects
     * and directories.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical directory path to delete.
     * </p>
     * @param bool $recursive [optional] <p>
     * Whether descendant stored objects and directories should also be deleted.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function deleteDirectory (PathContract $directory, bool $recursive = false):void {

        $directory = $this->normalizePath($directory);

        $this->validateAncestors($directory);

        $key = $this->key($directory);

        if (isset($this->objects[$key]))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $directory->value()
                ]
            );

        if (!isset($this->directories[$key]))
            return;

        if ($recursive)

            $this->deleteDescendants($directory);

        else if (!$this->isDirectoryEmpty($directory))

            throw new DirectoryNotEmptyException(
                context: [
                    'path' => $directory->value()
                ]
            );

        unset($this->directories[$key]);

    }

    /**
     * ### Copies a storage directory
     *
     * Recursively copies the specified source directory and its descendant stored regular objects and directories to
     * a new destination.
     *
     * Empty directories are preserved. The destination parent directory must already exist. Copying an existing
     * directory to itself has no effect.
     *
     * Copying a directory into one of its descendants is not permitted.
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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::normalizePath() To normalize logical storage paths.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateDirectoryDestination() To validate the source and
     * destination directory relationship.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve the internal destination directory key.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::transferDescendants() To copy descendant stored objects and
     * directories.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $source <p>
     * Logical path of the source directory.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Storage\Path $destination <p>
     * Logical path of the destination directory.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\DirectoryNotFoundException If the source directory does not
     * exist.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the destination path identifies
     * an existing directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidDirectoryDestinationException If the destination path
     * is not a descendant of the source.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function copyDirectory (PathContract $source, PathContract $destination):void {

        $source = $this->normalizePath($source);
        $destination = $this->normalizePath($destination);

        if (!$this->validateDirectoryDestination($source, $destination))
            return;

        $this->directories[$this->key($destination)] = true;

        $this->transferDescendants($source, $destination, false);

    }

    /**
     * ### Moves a storage directory
     *
     * Relocates the specified source directory and its descendant stored regular objects and directories to a new
     * destination.
     *
     * Empty directories and relative descendant paths are preserved. The destination parent directory must already
     * exist. Moving an existing directory to itself has no effect.
     *
     * Moving a directory into one of its descendants is not permitted.
     *
     * <code>
     * $driver->moveDirectory(
     *     new Path('documents'),
     *     new Path('archive')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::normalizePath() To normalize logical storage paths.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateDirectoryDestination() To validate the source and
     * destination directory relationship.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve internal storage directory keys.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::transferDescendants() To relocate descendant stored objects
     * and directories.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $source <p>
     * Logical path of the source directory.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Storage\Path $destination <p>
     * Logical path of the destination directory.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\DirectoryNotFoundException If the source directory does not
     * exist.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the destination path identifies
     * an existing directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidDirectoryDestinationException If the destination path
     * is not a descendant of the source.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function moveDirectory (PathContract $source, PathContract $destination):void {

        $source = $this->normalizePath($source);
        $destination = $this->normalizePath($destination);

        if (!$this->validateDirectoryDestination($source, $destination))
            return;

        $this->directories[$this->key($destination)] = true;

        $this->transferDescendants($source, $destination, true);

        unset($this->directories[$this->key($source)]);

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

        return self::KEY_PREFIX.$this->normalizePath($path)->value();

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
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::normalizePath() To normalize the logical directory path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateAncestors() To validate the logical directory path
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

        $directory = $this->normalizePath($directory);

        $this->validateAncestors($directory);

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

    /**
     * ### Normalizes a logical storage path
     *
     * Converts a storage path contract implementation into a validated Foundation storage path.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Storage\Path::value() To retrieve the logical storage path value.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical storage path to normalize.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Foundation\IO\Storage\Path Normalized storage path.
     */
    private function normalizePath (PathContract $path):Path {

        return $path instanceof Path
            ? $path
            : new Path($path->value());

    }

    /**
     * ### Validates a logical storage object path
     *
     * Ensures that the specified logical path does not identify a directory and that its parent directory exists.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::normalizePath() To normalize the logical storage path.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateAncestors() To validate the logical path hierarchy.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical storage object path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Foundation\IO\Storage\Path Validated storage object path.
     */
    private function validateObjectPath (PathContract $path):Path {

        $path = $this->normalizePath($path);

        $this->validateAncestors($path);

        if (isset($this->directories[$this->key($path)]))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $path->value()
                ]
            );

        return $path;

    }

    /**
     * ### Validates a writable logical storage object path
     *
     * Ensures that the specified logical path can identify a stored regular object and that its parent directory
     * exists.
     *
     * The stored object itself is not required to exist.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateObjectPath() To validate the logical storage object
     * path.
     * @uses \FireHub\Foundation\IO\Storage\Path::parent() To retrieve the logical parent directory.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::directoryExists() To determine whether the parent
     * directory exists.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical storage object path to validate.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return \FireHub\Foundation\IO\Storage\Path Validated writable storage object path.
     */
    private function validateWritableObjectPath (PathContract $path):Path {

        $path = $this->validateObjectPath($path);

        $parent = $path->parent();

        if ($parent !== null && !$this->directoryExists($parent))
            throw new DirectoryNotFoundException(
                context: [
                    'path' => $parent->value()
                ]
            );

        return $path;

    }

    /**
     * ### Validates logical storage path ancestors
     *
     * Ensures that no ancestor of the specified logical storage path identifies a stored regular object.
     *
     * Missing ancestor directories are permitted.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Path::parent() To retrieve logical ancestor paths.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve internal storage keys.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $path <p>
     * Logical storage path whose ancestors should be validated.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    private function validateAncestors (Path $path):void {

        $parent = $path->parent();

        while ($parent !== null) {

            if (isset($this->objects[$this->key($parent)]))
                throw new InvalidObjectTypeException(
                    context: [
                        'path' => $parent->value()
                    ]
                );

            $parent = $parent->parent();

        }

    }

    /**
     * ### Determines whether a storage directory is empty
     *
     * Determines whether the specified storage directory contains any descendant-stored objects or directories.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve the internal storage directory key.
     * @uses \FireHub\Runtime\Arr\Access::keys() To retrieve the internal storage directory contents.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To identify descendant storage keys.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $directory <p>
     * Logical directory path to inspect.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return bool True if the directory contains no descendants.
     */
    private function isDirectoryEmpty (Path $directory):bool {

        $prefix = $this->key($directory).'/';

        foreach (Runtime\Arr\Access::keys($this->objects) as $key)
            if (Runtime\Str\SB\Search::startsWith($prefix, $key))
                return false;

        foreach (Runtime\Arr\Access::keys($this->directories) as $key)
            if (Runtime\Str\SB\Search::startsWith($prefix, $key))
                return false;

        return true;

    }

    /**
     * ### Deletes storage directory descendants
     *
     * Removes all stored regular objects and directories located within the specified logical directory hierarchy.
     *
     * The specified directory itself is not removed.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve the internal storage directory key.
     * @uses \FireHub\Runtime\Arr\Access::keys() To retrieve internal storage keys.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To identify descendant storage keys.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $directory <p>
     * Logical directory whose descendants should be deleted.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    private function deleteDescendants (Path $directory):void {

        $prefix = $this->key($directory).'/';

        foreach (Runtime\Arr\Access::keys($this->objects) as $key)
            if (Runtime\Str\SB\Search::startsWith($prefix, $key))
                unset($this->objects[$key]);

        foreach (Runtime\Arr\Access::keys($this->directories) as $key)
            if (Runtime\Str\SB\Search::startsWith($prefix, $key))
                unset($this->directories[$key]);

    }

    /**
     * ### Validates a directory transfer destination
     *
     * Validates the existence of the source directory, the availability of the destination path, and the
     * relationship between both paths.
     *
     * Returns false if both paths identify the same existing directory.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::validateAncestors() To validate the logical directory path
     * hierarchy.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve internal storage keys.
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::directoryExists() To determine whether the destination
     * parent directory exists.
     * @uses \FireHub\Foundation\IO\Storage\Path::parent() To retrieve the logical destination parent directory.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To determine whether the destination is a descendant of the
     * source.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $source <p>
     * Logical source directory path.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Path $destination <p>
     * Logical destination directory path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\DirectoryNotFoundException If the source directory does not
     * exist.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If the destination path identifies
     * an existing directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidDirectoryDestinationException If the destination path
     * is not a descendant of the source.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return bool True if the directory transfer should proceed.
     */
    private function validateDirectoryDestination (Path $source, Path $destination):bool {

        $this->validateAncestors($source);
        $this->validateAncestors($destination);

        $source_key = $this->key($source);
        $destination_key = $this->key($destination);

        if (isset($this->objects[$source_key]))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $source->value()
                ]
            );

        if (!isset($this->directories[$source_key]))
            throw new DirectoryNotFoundException(
                context: [
                    'path' => $source->value()
                ]
            );

        if ($source_key === $destination_key)
            return false;

        if (Runtime\Str\SB\Search::startsWith($source_key.'/', $destination_key))
            throw new InvalidDirectoryDestinationException(
                context: [
                    'source' => $source->value(),
                    'destination' => $destination->value()
                ]
            );

        if (isset($this->objects[$destination_key]))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $destination->value()
                ]
            );

        if (isset($this->directories[$destination_key]))
            throw new DirectoryAlreadyExistsException(
                context: [
                    'path' => $destination->value()
                ]
            );

        $parent = $destination->parent();

        if (!$this->directoryExists($parent))
            throw new DirectoryNotFoundException(
                context: [
                    'path' => $parent?->value()
                ]
            );

        return true;

    }

    /**
     * ### Transfers storage directory descendants
     *
     * Copies or relocates all descendant-stored regular objects and directories from the source directory to the
     * destination directory.
     *
     * Relative descendant paths and empty directories are preserved. This method does not transfer the source
     * directory itself.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Driver\Memory::key() To retrieve internal storage directory keys.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the source directory prefix length.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To identify descendant storage keys.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To retrieve relative descendant storage keys.
     * @uses \FireHub\Runtime\Arr\Access::keys() To retrieve internal storage directory keys.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $source <p>
     * Logical source directory path.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Path $destination <p>
     * Logical destination directory path.
     * </p>
     * @param bool $move <p>
     * Whether source descendants should be removed after being transferred.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    private function transferDescendants (Path $source, Path $destination, bool $move):void {

        $source_prefix = $this->key($source).'/';
        $destination_prefix = $this->key($destination).'/';

        $offset = Runtime\Str\SB\Inspection::length($source_prefix);

        foreach (Runtime\Arr\Access::keys($this->directories) as $key) {

            if (!Runtime\Str\SB\Search::startsWith($source_prefix, $key))
                continue;

            $target = $destination_prefix.Runtime\Str\SB\Access::part($key, $offset);

            $this->directories[$target] = true;

            if ($move)
                unset($this->directories[$key]);

        }

        foreach ($this->objects as $key => $contents) {

            if (!Runtime\Str\SB\Search::startsWith($source_prefix, $key))
                continue;

            $target = $destination_prefix.Runtime\Str\SB\Access::part($key, $offset);

            $this->objects[$target] = $contents;

            if ($move)
                unset($this->objects[$key]);

        }

    }

}