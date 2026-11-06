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
use FireHub\Core\Foundation\Constant\Environment\Php\PlatformInfo;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime\Stream\Enum\OpenMode;
use FireHub\Runtime;

/**
 * ### File Descriptor Stream
 *
 * Provides a readable, writable, and potentially seekable stream backed by PHP's php://fd wrapper.
 *
 * Allows accessing an existing file descriptor inherited or provided by the current process.
 *
 * Reading, writing, and positioning capabilities depend on the underlying descriptor and the selected opening mode.
 *
 * The php://fd wrapper is primarily intended for CLI environments and is not guaranteed to be available in other
 * PHP execution environments.
 *
 * The stream owns the PHP resource opened by this instance. Closing the stream releases that resource and may affect
 * the underlying file descriptor.
 * @since 1.0.0
 */
final class FileDesc extends ResourceStream implements Positionable, Seekable, Readable, Writable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the file descriptor stream.
     * @uses \FireHub\Core\Foundation\Constant\Environment\Php\PlatformInfo::SAPI To validate the environment.
     *
     * @param non-negative-int $descriptor <p>
     * Existing file descriptor number.
     * </p>
     * @param \FireHub\Runtime\Stream\Enum\OpenMode $mode [optional] <p>
     * Stream opening mode.
     *
     * Defaults to READ_BINARY.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the descriptor is invalid or cannot be opened.
     *
     * @return void
     */
    public function __construct (int $descriptor, OpenMode $mode = OpenMode::READ_BINARY) {

        if (PlatformInfo::SAPI !== 'cli')
            throw new StreamException(
                'File descriptor streams are supported only in CLI environments.'
            );

        if ($descriptor < 3)
            throw new StreamException(
                'Use standard stream classes for descriptors 0, 1, and 2.'
            );

        $resource = Runtime\Stream\Handle::open(
            "php://fd/$descriptor",
            $mode
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open file descriptor stream.'
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::readResourceContents() To read the remaining resource contents.
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::tellResource() To get the current stream position.
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::seekResource() To move the stream position.
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::rewindResource() To rewind the resource.
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceEof() To check whether the stream has reached EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function eof ():bool {

        return $this->resourceEof();

    }

}