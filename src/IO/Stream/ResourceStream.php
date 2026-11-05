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

namespace FireHub\Foundation\IO\Stream;

use FireHub\Core\Boundary\IO\Stream;
use FireHub\Core\Type\IO\SeekOrigin;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Abstract Resource Stream
 *
 * Provides shared functionality for streams backed by native PHP resources.
 *
 * Manages resource ownership, lifecycle, and common stream operations.
 *
 * Reading, writing, and positioning operations are protected, so concrete implementations can expose only the
 * capabilities they support.
 * @since 1.0.0
 */
abstract class ResourceStream implements Stream {

    /**
     * ### Native PHP stream resource
     * @var null|resource
     *
     * @since 1.0.0
     */
    private mixed $resource;

    /**
     * ### Resource ownership policy
     * @since 1.0.0
     */
    private readonly Ownership $ownership;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::resource() To validate the resource type.
     * @uses \FireHub\Runtime\ResourceManager::type() To validate the resource type.
     *
     * @param resource $resource <p>
     * Native PHP stream resource.
     * </p>
     * @param \FireHub\Foundation\IO\Stream\Ownership $ownership [optional] <p>
     * Determines whether the stream owns the underlying resource.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the resource is not a valid PHP stream.
     *
     * @return void
     */
    protected function __construct (mixed $resource, Ownership $ownership = Ownership::BORROWED) {

        if (
            !Runtime\DataIs::resource($resource)
            || Runtime\ResourceManager::type($resource) !== Runtime\Type\Resource::STREAM
        ) throw new StreamException(
            'The supplied resource must be a valid PHP stream.'
        );

        $this->resource = $resource;
        $this->ownership = $ownership;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::resource() To validate the resource type.
     */
    final public function isClosed ():bool {

        return !Runtime\DataIs::resource($this->resource);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::close() To close the underlying resource.
     * @uses \FireHub\Runtime\DataIs::resource() To validate the resource type.
     */
    final public function close ():void {

        if ($this->resource === null) return;

        $resource = $this->resource;

        $this->resource = null;

        if (
            $this->ownership === Ownership::OWNED
            && Runtime\DataIs::resource($resource)
        ) Runtime\Stream\Handle::close($resource);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Metadata::get() To get stream metadata.
     * @uses \FireHub\Runtime\Stream\Metadata::statistics() To get stream statistics.
     * @uses \FireHub\Runtime\DataIs::int() To validate the size type.
     * @uses \FireHub\Runtime\DataIs::string() To validate the URI type.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the URI starts with a specific prefix.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function size ():?int {

        $resource = $this->requireResource();

        $metadata = Runtime\Stream\Metadata::get($resource);

        $wrapper = $metadata['wrapper_type'] ?? null;
        $uri = $metadata['uri'] ?? null;

        $supported = $wrapper === 'plainfile'
            || (
                $wrapper === 'PHP'
                && Runtime\DataIs::string($uri)
                && (
                    Runtime\Str\SB\Search::startsWith('php://memory', $uri)
                    || Runtime\Str\SB\Search::startsWith('php://temp', $uri)
                )
            );

        if (!$supported) return null;

        $stat = Runtime\Stream\Metadata::statistics($resource);

        if ($stat === false) return null;

        $size = $stat['size'] ?? null;

        return Runtime\DataIs::int($size) && $size >= 0
            ? $size : null;

    }

    /**
     * ### Gets the active native stream resource
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::resource() To validate the resource type.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     *
     * @return resource Active PHP stream resource.
     */
    final protected function requireResource ():mixed {

        $resource = $this->resource;

        if (!Runtime\DataIs::resource($resource))
            throw new StreamException(
                'The stream is closed.'
            );

        return $resource;

    }

    /**
     * ### Checks whether the stream currently supports reading
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsClosed() To check if the stream is closed.
     * @uses \FireHub\Runtime\Stream\Metadata::get() To get stream metadata.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check if the mode contains a specific substring.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the mode starts with a specific prefix.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     *
     * @return bool True if the stream is readable.
     */
    final protected function resourceIsReadable ():bool {

        if ($this->isClosed()) return false;

        $metadata = Runtime\Stream\Metadata::get(
            $this->requireResource()
        );

        /** @var string $mode */
        $mode = $metadata['mode'] ?? '';

        return Runtime\Str\SB\Search::contains($mode, '+')
            || Runtime\Str\SB\Search::startsWith('r', $mode);

    }

    /**
     * ### Checks whether the stream currently supports writing
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsClosed() To check if the stream is closed.
     * @uses \FireHub\Runtime\Stream\Metadata::get() To get stream metadata.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check if the mode contains a specific substring.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the mode starts with a specific prefix.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     *
     * @return bool True if the stream is writable.
     */
    final protected function resourceIsWritable ():bool {

        if ($this->isClosed()) return false;

        $metadata = Runtime\Stream\Metadata::get(
            $this->requireResource()
        );

        /** @var string $mode */
        $mode = $metadata['mode'] ?? '';

        return Runtime\Str\SB\Search::contains($mode, '+')
            || Runtime\Str\SB\Search::startsWith('w', $mode)
            || Runtime\Str\SB\Search::startsWith('a', $mode)
            || Runtime\Str\SB\Search::startsWith('x', $mode)
            || Runtime\Str\SB\Search::startsWith('c', $mode);

    }

    /**
     * ### Checks whether the stream currently supports seeking
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsClosed() To check if the stream is closed.
     * @uses \FireHub\Runtime\Stream\Metadata::get() To get stream metadata.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     *
     * @return bool True if the stream is seekable.
     */
    final protected function resourceIsSeekable ():bool {

        if ($this->isClosed()) return false;

        $metadata = Runtime\Stream\Metadata::get(
            $this->requireResource()
        );

        return ($metadata['seekable'] ?? false) === true;

    }

    /**
     * ### Reads bytes from the resource
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsClosed() To check if the stream is closed.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsReadable() To check if the stream is readable.
     * @uses \FireHub\Runtime\Stream\Read::bytes() To read bytes from the stream.
     *
     * @param non-negative-int $length <p>
     * Maximum number of bytes to read.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     *
     * @return string Data read from the stream.
     */
    final protected function readResource (int $length):string {

        $resource = $this->requireResource();

        if (!$this->resourceIsReadable())
            throw new StreamException(
                'The stream is not readable.'
            );

        if ($length < 0)
            throw new StreamException(
                'Read length cannot be negative.'
            );

        if ($length === 0) return '';

        $data = Runtime\Stream\Read::bytes($resource, $length);

        if ($data === false)
            throw new StreamException(
                'Unable to read from the stream.'
            );

        return $data;

    }

    /**
     * ### Reads the remaining resource contents
     *
     * Reads from the current position to the end of the stream.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsClosed() To check if the stream is closed.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsReadable() To check if the stream is readable.
     * @uses \FireHub\Runtime\Stream\Read::contents() To read the stream contents.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     *
     * @return string Remaining stream contents.
     */
    final protected function readResourceContents ():string {

        $resource = $this->requireResource();

        if (!$this->resourceIsReadable())
            throw new StreamException(
                'The stream is not readable.'
            );

        $contents = Runtime\Stream\Read::contents($resource);

        if ($contents === false)
            throw new StreamException(
                'Unable to read stream contents.'
            );

        return $contents;

    }

    /**
     * ### Writes bytes to the resource
     * @since 1.0.0
     *
     * @uses \FreHub\Foundation\IO\Stream\ResourceStream::resourceIsClosed() To check if the stream is closed.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsWritable() To check if the stream is writable.
     * @uses \FireHub\Runtime\Stream\Write::bytes() To write bytes to the stream.
     *
     * @return non-negative-int <p>
     * Number of bytes written.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If writing fails.
     *
     * @return non-negative-int Number of bytes written.
     */
    final protected function writeResource (string $data):int {

        $resource = $this->requireResource();

        if (!$this->resourceIsWritable())
            throw new StreamException(
                'The stream is not writable.'
            );

        if ($data === '') return 0;

        $written = Runtime\Stream\Write::bytes($resource, $data);

        if ($written === false)
            throw new StreamException(
                'Unable to write to the stream.'
            );

        return $written;

    }

    /**
     * ### Gets the current resource position
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsClosed() To check if the stream is closed.
     * @uses \FireHub\Runtime\Stream\Position::tell() To get the current stream position.
     *
     * @return non-negative-int <p>
     * Current byte offset.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the position cannot be determined.
     */
    final protected function tellResource ():int {

        $position = Runtime\Stream\Position::tell(
            $this->requireResource()
        );

        if ($position === false || $position < 0)
            throw new StreamException(
                'Unable to determine the stream position.'
            );

        return $position;

    }

    /**
     * ### Moves the resource position
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::requireResource() To require the stream resource.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsSeekable() To check if the stream is seekable.
     * @uses \FireHub\Runtime\Stream\Position::seek() To move the stream position.
     *
     * @param int $offset <p>
     * Offset to move the position.
     * </p>
     * @param \FireHub\Core\Type\IO\SeekOrigin $origin [optional] <p>
     * Reference position used to calculate the new position.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If seeking fails.
     *
     * @return void
     */
    final protected function seekResource (int $offset, SeekOrigin $origin = SeekOrigin::START):void {

        $resource = $this->requireResource();

        if (!$this->resourceIsSeekable())
            throw new StreamException(
                'The stream is not seekable.'
            );

        if (Runtime\Stream\Position::seek($resource, $offset, $origin) !== 0)
            throw new StreamException(
                'Unable to seek within the stream.'
            );

    }

    /**
     * ### Rewinds the resource
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::seekResource() To move the stream position to the start.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If seeking fails.
     *
     * @return void
     */
    final protected function rewindResource ():void {

        $this->seekResource(0);

    }

    /**
     * ### Checks whether the resource has reached EOF
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::requireResource() To require the stream resource.
     * @uses \FireHub\Runtime\Stream\Position::eof() To check if EOF has been reached.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     *
     * @return bool True if EOF has been reached.
     */
    final protected function resourceEof ():bool {

        return Runtime\Stream\Position::eof(
            $this->requireResource()
        );

    }

}