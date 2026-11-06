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
 * ### Standard Error Stream
 *
 * Provides a writable stream backed by PHP's php://stderr stream wrapper.
 *
 * Allows writing diagnostic messages and errors to the standard error stream of the current process.
 *
 * Unlike php://stdout, this stream provides a separate output channel intended for errors and diagnostics.
 *
 * The stream is write-only and does not guarantee seeking or positioning capabilities.
 *
 * Closing this object does not close the underlying standard error stream.
 * @since 1.0.0
 */
final class StdError extends ResourceStream implements Writable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the standard error stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream cannot be opened.
     *
     * @return void
     */
    public function __construct () {

        $resource = Runtime\Stream\Handle::open(
            'php://stderr',
            Runtime\Stream\Enum\OpenMode::WRITE_BINARY
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open standard error stream.'
            );

        parent::__construct($resource);

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