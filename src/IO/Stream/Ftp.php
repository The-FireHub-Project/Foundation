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
 * ### FTP Stream
 *
 * Provides a readable or writable stream backed by PHP's ftp:// and ftps:// stream wrappers.
 *
 * Allows transferring files between the application and remote FTP servers using PHP's native stream functionality.
 *
 * The stream supports reading or writing depending on the selected opening mode but does not support both operations
 * simultaneously.
 *
 * Unlike local filesystem streams, FTP streams operate over a network connection and do not guarantee seeking or
 * positioning capabilities.
 *
 * Opening the stream may perform network I/O and can fail due to connection errors, authentication failures, server
 * restrictions, or timeouts.
 * @since 1.0.0
 */
final class Ftp extends ResourceStream implements Readable, Writable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the FTP stream.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To validate the URI scheme.
     *
     * @param non-empty-string $uri <p>
     * FTP or FTPS URI identifying the remote resource.
     * </p>
     * @param \FireHub\Runtime\Stream\Enum\OpenMode $mode [optional] <p>
     * Stream opening mode.
     *
     * Only READ_BINARY and WRITE_BINARY are supported.
     *
     * Defaults to READ_BINARY.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the URI scheme or opening mode is unsupported,
     * or the stream cannot be opened.
     *
     * @return void
     */
    public function __construct (string $uri, OpenMode $mode = OpenMode::READ_BINARY) {

        if (
            !Runtime\Str\SB\Search::startsWith('ftp://', $uri)
            && !Runtime\Str\SB\Search::startsWith('ftps://', $uri)
        ) throw new StreamException(
            'Only FTP and FTPS stream URIs are supported.'
        );

        if ($mode !== OpenMode::READ_BINARY && $mode !== OpenMode::WRITE_BINARY)
            throw new StreamException(
                'FTP streams support only READ_BINARY and WRITE_BINARY opening modes.'
            );

        $resource = Runtime\Stream\Handle::open(
            $uri,
            $mode
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open FTP stream.'
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