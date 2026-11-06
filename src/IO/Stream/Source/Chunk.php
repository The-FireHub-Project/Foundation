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

namespace FireHub\Foundation\IO\Stream\Source;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\DataStructure\Stream\Source;
use FireHub\Foundation\IO\Stream\Exception\StreamException;

/**
 * ### Byte Chunk Stream Source
 *
 * Adapts a readable I/O stream into a lazy sequence of byte chunks.
 *
 * Each element contains up to the configured number of bytes.
 *
 * Iteration consumes the underlying stream from its current position without automatically rewinding or closing it.
 *
 * This source is intended for blocking streams. An empty read before EOF is treated as an error.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<int, string>
 */
final readonly class Chunk implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * The readable stream used as the source of bytes.
     * </p>
     * @param positive-int $size [optional] <p>
     * Maximum number of bytes per chunk.
     *
     * Defaults to 8192.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the chunk size is not positive.
     *
     * @return void
     */
    public function __construct (
        private Readable $stream,
        private int $size = 8192
    ) {

        if ($size <= 0)
            throw new StreamException(
                'Chunk size must be greater than zero.'
            );

    }

    /**
     * ### Iterates over byte chunks
     *
     * Reads data lazily from the current stream position.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Readable::read() To read data from the stream.
     * @uses \FireHub\Foundation\IO\Stream\Readable::eof() To check if the stream has reached EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream cannot make progress before EOF.
     *
     * @return iterable<int, string> Byte chunks.
     */
    public function iterate ():iterable {

        $index = 0;

        while (!$this->stream->eof()) {

            $chunk = $this->stream->read($this->size);

            if ($chunk === '') {

                if ($this->stream->eof()) // @phpstan-ignore if.alwaysFalse
                    break;

                throw new StreamException(
                    'The stream returned no data before reaching EOF.'
                );

            }

            yield $index++ => $chunk;

        }

    }

}