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
 * ### Stream Writer
 *
 * Provides high-level writing operations for writable streams.
 *
 * Ensures that the complete supplied contents are written by handling partial writes and detecting streams that
 * cannot make progress.
 *
 * Writing starts at the current stream position and may advance it.
 *
 * Streams are not automatically rewound, flushed, or closed.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 */
final readonly class Writer {

    /**
     * ### Writes complete contents to a stream
     *
     * Repeatedly writes the remaining contents until all supplied bytes have been accepted by the destination stream.
     *
     * <code>
     * $stream = new Memory;
     *
     * Writer::write($stream, 'FireHub');
     * // 7
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Writer::validate() To validate the destination stream.
     * @uses \FireHub\Foundation\IO\Stream\Writer::writeContents() To write the complete contents.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $stream <p>
     * Destination stream.
     * </p>
     * @param string $contents <p>
     * Textual or binary contents to write.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the destination stream cannot accept the
     * complete contents.
     *
     * @return non-negative-int Number of bytes written.
     */
    public static function write (Writable $stream, string $contents):int {

        self::validate($stream);

        return self::writeContents(
            $stream,
            $contents
        );

    }

    /**
     * ### Writes a line to a stream
     *
     * Writes the supplied contents followed by the specified line separator.
     *
     * <code>
     * $stream = new Memory;
     *
     * Writer::line($stream, 'FireHub');
     * Writer::line($stream, 'Foundation', "\r\n");
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Writer::write() To write the line and its separator.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $stream <p>
     * Destination stream.
     * </p>
     * @param string $contents <p>
     * Line contents.
     * </p>
     * @param string $separator [optional] <p>
     * Line separator appended to the supplied contents.
     *
     * Defaults to a line feed.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the complete line cannot be written.
     *
     * @return non-negative-int Number of bytes written, including the separator.
     */
    public static function line (Writable $stream, string $contents, string $separator = "\n"):int {

        return self::write(
            $stream,
            $contents.$separator
        );

    }

    /**
     * ### Writes multiple lines to a stream
     *
     * Writes each supplied line followed by the specified line separator.
     *
     * Lines are consumed incrementally without materializing the entire iterable in memory.
     *
     * <code>
     * $stream = new Memory;
     *
     * Writer::lines(
     *     $stream,
     *     ['FireHub', 'Foundation', 'Runtime']
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Writer::validate() To validate the destination stream.
     * @uses \FireHub\Foundation\IO\Stream\Writer::writeContents() To write each line and its separator.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $stream <p>
     * Destination stream.
     * </p>
     * @param iterable<string> $lines <p>
     * Iterable collection of lines to write.
     * </p>
     * @param string $separator [optional] <p>
     * Line separator appended to each line.
     *
     * Defaults to a line feed.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If a complete line cannot be written.
     *
     * @return non-negative-int Total number of bytes written, including separators.
     */
    public static function lines (Writable $stream, iterable $lines, string $separator = "\n"):int {

        self::validate($stream);

        $written = 0;

        foreach ($lines as $line)
            $written += self::writeContents(
                $stream,
                $line.$separator
            );

        return $written;

    }

    /**
     * ### Validates the destination stream
     *
     * Ensures that the supplied stream is open and supports writing.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream::isClosed() To determine whether the stream is closed.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isWritable() To determine whether the stream supports writing.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $stream <p>
     * Destination stream.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the destination stream is closed or not
     * writable.
     *
     * @return void
     */
    private static function validate (Writable $stream):void {

        if ($stream->isClosed())
            throw new StreamException(
                'The destination stream is closed.'
            );

        if (!$stream->isWritable())
            throw new StreamException(
                'The destination stream is not writable.'
            );

    }

    /**
     * ### Writes complete contents to a validated stream
     *
     * Handles partial writes by repeatedly writing the remaining bytes.
     *
     * A write operation that accepts zero bytes before completion is treated as a failure.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::write() To write the remaining contents.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the content length.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To retrieve the remaining contents.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $stream <p>
     * Validated destination stream.
     * </p>
     * @param string $contents <p>
     * Contents to write.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream cannot make progress or reports
     * an invalid write length.
     *
     * @return non-negative-int Number of bytes written.
     */
    private static function writeContents (Writable $stream, string $contents):int {

        $length = Runtime\Str\SB\Inspection::length($contents);
        $offset = 0;

        while ($offset < $length) {

            $remaining = Runtime\Str\SB\Access::part(
                $contents,
                $offset
            );

            $written = $stream->write($remaining);

            if ($written <= 0)
                throw new StreamException(
                    'The destination stream could not accept the remaining data.'
                );

            if ($written > $length - $offset)
                throw new StreamException(
                    'The destination stream reported an invalid write length.'
                );

            $offset += $written;

        }

        return $offset;

    }

}