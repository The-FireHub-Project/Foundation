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
use FireHub\Runtime\Stream\Enum\OpenMode;
use FireHub\Runtime;

/**
 * ### File Stream
 *
 * Provides a readable, writable, and seekable stream backed by PHP's file:// stream wrapper.
 *
 * Allows reading, writing, and positioning within local filesystem files using a configurable opening mode.
 *
 * Unlike php://memory and php://temp, this stream operates on persistent filesystem resources.
 *
 * Reading and writing availability depend on the selected opening mode, while seeking depends on the underlying
 * resource capabilities.
 * @since 1.0.0
 */
final class File extends ResourceStream implements Positionable, Seekable, Readable, Writable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the file stream.
     *
     * @param non-empty-string $path <p>
     * Local filesystem path or file:// URI.
     * </p>
     * @param \FireHub\Runtime\Stream\Enum\OpenMode $mode [optional] <p>
     * File opening mode.
     *
     * Defaults to READ_BINARY.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the file cannot be opened.
     *
     * @return void
     */
    public function __construct (string $path, OpenMode $mode = OpenMode::READ_BINARY) {

        $resource = Runtime\Stream\Handle::open(
            $path,
            $mode
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open file stream.'
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