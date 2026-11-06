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
use FireHub\Runtime;

/**
 * ### Stream Copier
 *
 * Provides memory-bounded copying between readable and writable streams.
 *
 * Data is transferred incrementally without materializing the entire source stream in memory.
 *
 * Partial reads and writes are supported.
 *
 * Copying starts at the current stream positions.
 *
 * Streams are not automatically rewound, flushed, or closed.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 */
final readonly class Copier {

    /**
     * ### Copies data between streams
     *
     * Copies bytes from the source into the destination until EOF is reached or the optional byte limit has been
     * satisfied.
     *
     * Each chunk is completely written before another chunk is read.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Copier::validate() To validate the copy operation.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copyChunks() To transfer data between streams.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $source <p>
     * Source stream.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $destination <p>
     * Destination stream.
     * </p>
     * @param positive-int $buffer_size [optional] <p>
     * Maximum number of bytes requested per read operation.
     *
     * Defaults to 65536.
     * </p>
     * @param null|non-negative-int $limit [optional] <p>
     * Maximum number of bytes to copy.
     *
     * Null copies until EOF.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the copy operation cannot be completed.
     *
     * @return non-negative-int Number of bytes accepted by the destination.
     */
    public static function copy (
        Readable $source,
        Writable $destination,
        int $buffer_size = 65536,
        ?int $limit = null
    ):int {

        self::validate(
            $source,
            $destination,
            $buffer_size,
            $limit
        );

        if ($limit === 0) return 0;

        return self::copyChunks(
            $source,
            $destination,
            $buffer_size,
            $limit
        );

    }

    /**
     * ### Validates the copy operation
     *
     * Ensures that the buffer size, byte limit, source, and destination satisfy the requirements for copying.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the source is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isWritable() To check if the destination is writable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isClosed() To check if the source is closed.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isClosed() To check if the destination is closed.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $source <p>
     * Source stream.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $destination <p>
     * Destination stream.
     * </p>
     * @param int $buffer_size <p>
     * Requested buffer size.
     * </p>
     * @param null|int $limit <p>
     * Maximum number of bytes to copy.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the copy parameters or streams are invalid.
     *
     * @return void
     */
    private static function validate (Readable $source, Writable $destination, int $buffer_size, ?int $limit):void {

        if ($buffer_size <= 0)
            throw new StreamException(
                'Buffer size must be greater than zero.'
            );

        if ($limit !== null && $limit < 0)
            throw new StreamException(
                'Copy limit cannot be negative.'
            );

        if ($source->isClosed())
            throw new StreamException(
                'The source stream is closed.'
            );

        if ($destination->isClosed())
            throw new StreamException(
                'The destination stream is closed.'
            );

        if (!$source->isReadable())
            throw new StreamException(
                'The source stream is not readable.'
            );

        if (!$destination->isWritable())
            throw new StreamException(
                'The destination stream is not writable.'
            );

    }

    /**
     * ### Copies stream chunks
     *
     * Coordinates reading and writing until EOF or the byte limit is reached.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Copier::copyChunks() To transfer data between streams.
     * @uses \FireHub\Foundation\IO\Stream\Copier::readChunk() To read the next chunk.
     * @uses \FireHub\Foundation\IO\Stream\Copier::writeChunk() To write the entire chunk.
     * @uses \FireHub\Runtime\Math::min() To clamp the buffer size to the limit.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $source <p>
     * Source stream.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $destination <p>
     * Destination stream.
     * </p>
     * @param positive-int $buffer_size <p>
     * Maximum read size.
     * </p>
     * @param null|non-negative-int $limit <p>
     * Maximum number of bytes to copy.
     * </p>
     *
     * @uses self::readChunk() To read the next chunk.
     * @uses self::writeChunk() To write the entire chunk.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading or writing fails.
     *
     * @return non-negative-int Number of bytes copied.
     */
    private static function copyChunks (
        Readable $source, Writable $destination, int $buffer_size, ?int $limit):int {

        $copied = 0;

        while (true) {

            if ($limit !== null && $copied >= $limit)
                break;

            /** @var positive-int $length */
            $length = $limit === null
                ? $buffer_size
                : Runtime\Math::min($buffer_size, $limit - $copied);

            $chunk = self::readChunk($source, $length);

            if ($chunk === '') break;

            $copied += self::writeChunk(
                $destination,
                $chunk
            );

        }

        return $copied;

    }

    /**
     * ### Reads the next chunk
     *
     * Reads up to the requested number of bytes.
     *
     * Returns an empty string only when EOF has been reached.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check if EOF has been reached.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read the next chunk.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To check the length of the returned chunk.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $source <p>
     * Source stream.
     * </p>
     * @param positive-int $length <p>
     * Maximum number of bytes to read.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the source cannot make progress or returns
     * more bytes than requested.
     *
     * @return string Data read from the source.
     */
    private static function readChunk (Readable $source, int $length):string {

        if ($source->eof()) return '';

        $chunk = $source->read($length);

        if ($chunk === '') {

            if ($source->eof()) return ''; // @phpstan-ignore if.alwaysFalse

            throw new StreamException(
                'The source stream returned no data before reaching EOF.'
            );

        }

        if (Runtime\Str\SB\Inspection::length($chunk) > $length)
            throw new StreamException(
                'The source stream returned more bytes than requested.'
            );

        return $chunk;

    }

    /**
     * ### Writes an entire chunk
     *
     * Handles partial writes by repeatedly writing the remaining bytes.
     *
     * An underlying writing that accepts zero bytes is treated as an error.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get a part of the string.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::write() To write the entire chunk.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $destination <p>
     * Destination stream.
     * </p>
     * @param string $chunk <p>
     * Data to write.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the destination cannot make progress or
     * reports an invalid write length.
     *
     * @return non-negative-int Number of bytes accepted by the destination.
     */
    private static function writeChunk (Writable $destination, string $chunk):int {

        $length = Runtime\Str\SB\Inspection::length($chunk);
        $offset = 0;

        while ($offset < $length) {

            $remaining = Runtime\Str\SB\Access::part(
                $chunk,
                $offset
            );

            $written = $destination->write($remaining);

            if ($written <= 0)
                throw new StreamException(
                    'The destination stream could not accept the remaining data.'
                );

            if ($written > Runtime\Str\SB\Inspection::length($remaining))
                throw new StreamException(
                    'The destination stream reported an invalid write length.'
                );

            $offset += $written;

        }

        return $offset;

    }

}