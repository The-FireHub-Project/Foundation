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

use FireHub\Core\Boundary\IO\Stream\Writable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Output Stream
 *
 * Provides a writable stream backed by PHP's php://output stream wrapper.
 *
 * Writes data directly to PHP's output mechanism, allowing content to be sent to the client or captured by an active
 * output buffer.
 *
 * Unlike php://memory and php://temp, this stream does not store written content for subsequent reading.
 *
 * The stream is write-only and does not support seeking or rewinding.
 * @since 1.0.0
 */
final class Output extends ResourceStream implements Writable {

    /**
     * ### Constructor
     *
     * Opens PHP's output stream for writing.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the php://output stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the output stream cannot be opened.
     *
     * @return void
     */
    public function __construct () {

        $resource = Runtime\Stream\Handle::open(
            'php://output',
            Runtime\Stream\Enum\OpenMode::WRITE_BINARY
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open output stream.'
            );

        parent::__construct($resource, Ownership::OWNED);

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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::writeResource() To write to the resource.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If writing fails.
     */
    public function write (string $data):int {

        return $this->writeResource($data);

    }

}