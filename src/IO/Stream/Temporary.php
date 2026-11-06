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

use FireHub\Core\Boundary\IO\Stream\ {
    Positionable, Readable, Seekable, Writable
};
use FireHub\Core\Type\IO\SeekOrigin;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Temporary Stream
 *
 * Provides a readable, writable, and seekable stream backed by PHP's php://temp stream wrapper.
 *
 * Stream contents are initially stored in memory. When the configured memory threshold is exceeded, PHP automatically
 * moves the contents to a temporary file on disk.
 *
 * Unlike php://memory, this stream can handle large amounts of data without keeping the entire contents in memory.
 * @since 1.0.0
 */
final class Temporary extends ResourceStream implements Positionable, Seekable, Readable, Writable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the php://temp stream.
     *
     * @param non-negative-int $memory_threshold [optional] <p>
     * Maximum number of bytes stored in memory before switching to temporary file storage.
     *
     * Defaults to 2 MiB (2,097,152 bytes).
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the memory threshold is invalid or the stream cannot be opened.
     *
     * @return void
     */
    public function __construct (int $memory_threshold = 2097152) {

        if ($memory_threshold < 0)
            throw new StreamException(
                'Maximum memory threshold cannot be negative.'
            );

        $resource = Runtime\Stream\Handle::open(
            "php://temp/maxmemory:$memory_threshold",
            Runtime\Stream\Enum\OpenMode::READ_WRITE_BINARY
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open temporary stream.'
            );

        parent::__construct($resource, Ownership::OWNED);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsReadable() To check if the resource is readable.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function isReadable ():bool {

        return $this->resourceIsReadable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsWritable() To check if the resource is writable.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function isWritable ():bool {

        return $this->resourceIsWritable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsSeekable() To check if the resource is seekable.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function isSeekable ():bool {

        return $this->resourceIsSeekable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::readResource() To read from the resource.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     */
    public function read (int $length):string {

        return $this->readResource($length);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::readResourceContents() To read the remaining contents of the resource.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     */
    public function contents ():string {

        return $this->readResourceContents();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::writeResource() To write to the resource.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If writing fails.
     */
    public function write (string $data):int {

        return $this->writeResource($data);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::tellResource() To get the current position in the resource.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the position cannot be determined.
     */
    public function tell ():int {

        return $this->tellResource();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::seekResource() To seek to a specific position in the resource.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If seeking fails.
     */
    public function seek (int $offset, SeekOrigin $origin = SeekOrigin::START):void {

        $this->seekResource($offset, $origin);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::rewindResource() To rewind the resource to the beginning.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If seeking fails.
     */
    public function rewind ():void {

        $this->rewindResource();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceEof() To check if the resource is at the end of the stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function eof ():bool {

        return $this->resourceEof();

    }

}