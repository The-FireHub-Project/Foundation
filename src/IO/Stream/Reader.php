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

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\DataStructure\Stream;
use FireHub\Foundation\IO\Stream\Source\ {
    Byte, Chunk, Line
};

/**
 * ### Stream Reader
 *
 * Provides factories for creating lazy data structure streams from readable I/O streams.
 * @since 1.0.0
 */
final readonly class Reader {

    /**
     * ### Chunks
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
     * @return \FireHub\Foundation\DataStructure\Stream<int, string> The stream of chunks.
     */
    public static function chunks (Readable $stream, int $size = 8192):Stream {

        return new Stream(new Chunk($stream, $size));

    }

    /**
     * ### Lines
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Source stream.
     * </p>
     * @param positive-int $chunk_size [optional] <p>
     * Maximum bytes read per operation.
     *
     * Defaults to 8192.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the chunk size is invalid.
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, string> The stream of lines.
     */
    public static function lines (Readable $stream, int $chunk_size = 8192):Stream {

        return new Stream(new Line($stream, $chunk_size));

    }

    /**
     * ### Bytes
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
     * @return \FireHub\Foundation\DataStructure\Stream<int, string> The stream of bytes.
     */
    public static function bytes (Readable $stream, int $chunk_size = 8192):Stream {

        return new Stream(new Byte($stream, $chunk_size));

    }

}