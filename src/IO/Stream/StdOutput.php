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
 * ### Standard Output Stream
 *
 * Provides a writable stream backed by PHP's php://stdout stream wrapper.
 *
 * Allows writing data to the standard output stream of the current process.
 *
 * Unlike php://output, this stream writes directly to the process standard output and is not affected by PHP's output
 * buffering mechanism.
 *
 * The stream is write-only and does not guarantee seeking or positioning capabilities.
 *
 * Closing this object does not close the underlying standard output stream.
 * @since 1.0.0
 */
final class StdOutput extends ResourceStream implements Writable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the standard output stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream cannot be opened.
     *
     * @return void
     */
    public function __construct () {

        $resource = Runtime\Stream\Handle::open(
            'php://stdout',
            Runtime\Stream\Enum\OpenMode::WRITE_BINARY
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open standard output stream.'
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