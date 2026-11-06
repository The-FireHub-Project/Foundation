<?php
declare(strict_types=1);

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

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### HTTP Stream
 *
 * Provides a readable stream backed by PHP's http:// and https:// stream wrappers.
 *
 * Allows reading data from remote HTTP and HTTPS resources using PHP's native stream functionality.
 *
 * The stream is read-only and does not guarantee seeking or positioning capabilities.
 *
 * Unlike local filesystem streams, HTTP streams retrieve their contents over a network connection.
 *
 * Opening the stream may perform network I/O and can fail due to connection errors, timeouts, or HTTP response errors.
 * @since 1.0.0
 */
final class Http extends ResourceStream implements Readable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the HTTP stream.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To validate the URI scheme.
     *
     * @param non-empty-string $uri <p>
     * HTTP or HTTPS URI identifying the remote resource.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the URI scheme is unsupported or the stream cannot be opened.
     *
     * @return void
     */
    public function __construct (string $uri) {

        if (
            !Runtime\Str\SB\Search::startsWith('http://', $uri)
            && !Runtime\Str\SB\Search::startsWith('https://', $uri)
        ) throw new StreamException(
            'Only HTTP and HTTPS stream URIs are supported.'
        );

        $resource = Runtime\Stream\Handle::open(
            $uri,
            Runtime\Stream\Enum\OpenMode::READ_BINARY
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open HTTP stream.'
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
    public function isReadable(): bool {

        return $this->resourceIsReadable();

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
    public function read(int $length): string {

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
    public function contents(): string {

        return $this->readResourceContents();

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
    public function eof(): bool {

        return $this->resourceEof();

    }

}