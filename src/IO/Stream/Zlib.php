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
    Readable, Writable
};
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime\Stream\Enum\OpenMode;
use FireHub\Runtime;

/**
 * ### Zlib Stream
 *
 * Provides a readable and writable stream backed by PHP's compress.zlib:// stream wrapper.
 *
 * Allows reading and writing gzip-compressed files using transparent compression and decompression.
 *
 * Reading and writing availability depend on the selected opening mode.
 *
 * This abstraction does not guarantee seeking and positioning.
 *
 * Requires the PHP zlib extension.
 * @since 1.0.0
 */
final class Zlib extends ResourceStream implements Readable, Writable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the compressed stream.
     * @uses \FireHub\Runtime\System\Extension::hasExtension() To validate the extension availability.
     *
     * @param non-empty-string $path <p>
     * Path to the gzip-compressed file.
     * </p>
     * @param \FireHub\Runtime\Stream\Enum\OpenMode $mode [optional] <p>
     * Stream opening mode.
     *
     * Only READ_BINARY and WRITE_BINARY are supported.
     *
     * Defaults to READ_BINARY.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the extension is unavailable, the opening
     * mode is unsupported, or the stream cannot be opened.
     *
     * @return void
     */
    public function __construct (string $path, OpenMode $mode = OpenMode::READ_BINARY) {

        if (!Runtime\System\Extension::hasExtension('zlib'))
            throw new StreamException(
                'The zlib extension is required to open compressed streams.'
            );

        if ($path === '')
            throw new StreamException(
                'The compressed file path cannot be empty.'
            );

        if (
            $mode !== OpenMode::READ_BINARY
            && $mode !== OpenMode::WRITE_BINARY
        ) throw new StreamException(
            'Zlib streams support only READ_BINARY and WRITE_BINARY opening modes.'
        );

        $resource = Runtime\Stream\Handle::open(
            "compress.zlib://$path",
            $mode
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open zlib stream.'
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceEof() To check if the resource has reached EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function eof ():bool {

        return $this->resourceEof();

    }

}