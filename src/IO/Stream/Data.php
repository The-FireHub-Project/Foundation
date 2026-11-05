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
    Positionable, Readable, Seekable
};
use FireHub\Core\Type\IO\SeekOrigin;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Data Stream
 *
 * Provides a readable and seekable stream backed by PHP's data:// stream wrapper.
 *
 * Allows reading inline data embedded directly within a URI, including plain text and Base64-encoded content.
 *
 * Unlike filesystem and network streams, this stream retrieves its contents directly from the supplied data URI.
 *
 * The stream is read-only and supports positioning within the decoded data.
 *
 * Opening the stream requires PHP's allow_url_fopen configuration directive to be enabled.
 * @since 1.0.0
 */
final class Data extends ResourceStream implements Positionable, Seekable, Readable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the data stream.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To validate the URI scheme.
     *
     * @param non-empty-string $uri <p>
     * Data URI containing inline content.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the URI is invalid or the stream cannot be opened.
     *
     * @return void
     */
    public function __construct (string $uri) {

        if (!Runtime\Str\SB\Search::startsWith('data:', $uri))
            throw new StreamException(
                'Only data stream URIs are supported.'
            );

        $resource = Runtime\Stream\Handle::open(
            $uri,
            Runtime\Stream\Enum\OpenMode::READ_BINARY
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open data stream.'
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceEof() To check if the resource has reached EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function eof ():bool {

        return $this->resourceEof();

    }

}