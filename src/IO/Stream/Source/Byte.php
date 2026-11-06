<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Stream\Source;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\DataStructure\Stream\Source;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Byte Stream Source
 *
 * Adapts a readable I/O stream into a lazy sequence of individual bytes.
 *
 * Each element is a binary string containing exactly one byte.
 *
 * This source does not perform character encoding or Unicode decoding.
 *
 * Iteration consumes the stream from its current position without automatically rewinding or closing it.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<int, string>
 */
final readonly class Byte implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Source stream.
     * </p>
     * @param positive-int $chunk_size [optional] <p>
     * Number of bytes read per operation.
     *
     * Defaults to 8192.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the chunk size is invalid.
     *
     * @return void
     */
    public function __construct (
        private Readable $stream,
        private int $chunk_size = 8192
    ) {

        if ($chunk_size <= 0)
            throw new StreamException(
                'Chunk size must be greater than zero.'
            );

    }

    /**
     * ### Iterates over individual bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Source\Chunk::iterate() To iterate over chunks.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of a string.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream cannot make progress before EOF.
     *
     * @return iterable<int, string> Individual bytes.
     */
    public function iterate ():iterable {

        $index = 0;

        foreach (new Chunk($this->stream, $this->chunk_size)->iterate() as $chunk) {

            $length = Runtime\Str\SB\Inspection::length($chunk);

            for ($position = 0; $position < $length; $position++)
                yield $index++ => $chunk[$position];

        }

    }

}