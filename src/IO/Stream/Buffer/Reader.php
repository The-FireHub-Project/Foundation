<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Stream\Buffer;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Buffered Reader
 *
 * Provides buffered reading for an existing readable stream.
 *
 * Reduces the number of underlying read operations by reading larger blocks into an internal buffer.
 *
 * The underlying stream remains owned by its original owner.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 */
final class Reader implements Readable {

    /**
     * ### Internal read buffer
     * @since 1.0.0
     */
    private string $buffer = '';

    /**
     * ### Current position within the buffer
     * @since 1.0.0
     */
    private int $position = 0;

    /**
     * ### Closed state
     * @since 1.0.0
     */
    private bool $closed = false;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Underlying readable stream.
     * </p>
     * @param positive-int $buffer_size [optional] <p>
     * Maximum number of bytes to prefetch.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffer size is invalid.
     *
     * @return void
     */
    public function __construct (
        private readonly Readable $stream,
        private readonly int $buffer_size = 8192
    ) {

        if ($buffer_size <= 0)
            throw new StreamException(
                'Buffer size must be greater than zero.'
            );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::isClosed() To check if the underlying stream is closed.
     */
    public function isClosed ():bool {

        return $this->closed || $this->stream->isClosed();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::isReadable() To check if the underlying stream is readable.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::isClosed() To check if the buffered reader is closed.
     */
    public function isReadable ():bool {

        return !$this->isClosed() && $this->stream->isReadable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::size() To get the size of the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed.
     */
    public function size ():?int {

        $this->requireOpen();

        return $this->stream->size();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::fill() To fill the internal buffer.s
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the internal buffer.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get a part of the internal buffer.
     * @uses \FireHub\Runtime\Math::min() To get the minimum of two values.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed, or the read
     * length is invalid.
     */
    public function read (int $length):string {

        $this->requireOpen();

        if ($length < 0)
            throw new StreamException(
                'Read length cannot be negative.'
            );

        if ($length === 0) return '';

        $result = '';

        while (Runtime\Str\SB\Inspection::length($result) < $length) {

            $available = Runtime\Str\SB\Inspection::length($this->buffer) - $this->position;

            if ($available === 0) {

                if ($this->stream->eof()) break;

                $this->fill();

                $available = Runtime\Str\SB\Inspection::length($this->buffer);

                if ($available === 0) break;

            }

            $remaining = $length - Runtime\Str\SB\Inspection::length($result);

            $take = Runtime\Math::min($remaining, $available);

            $result .= Runtime\Str\SB\Access::part(
                $this->buffer,
                $this->position,
                $take
            );

            $this->position += $take;

            if ($this->position === Runtime\Str\SB\Inspection::length($this->buffer)) {

                $this->buffer = '';
                $this->position = 0;

            }

        }

        return $result;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::read() To read from the underlying stream.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::size() To get the size of the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed, or the stream
     * returns no data before reaching EOF.
     */
    public function contents ():string {

        $this->requireOpen();

        $contents = '';

        while (!$this->eof()) {

            $chunk = $this->read($this->buffer_size);

            if ($chunk === '') {

                if ($this->eof()) break; // @phpstan-ignore if.alwaysFalse

                throw new StreamException(
                    'The stream returned no data before reaching EOF.'
                );

            }

            $contents .= $chunk;

        }

        return $contents;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::unreadBytes() To get the number of unread buffered bytes.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::eof() To check if the underlying stream is at EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed.
     */
    public function eof ():bool {

        $this->requireOpen();

        return $this->unreadBytes() === 0
            && $this->stream->eof();

    }

    /**
     * ### Gets the number of unread buffered bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the internal buffer.
     *
     * @return non-negative-int Number of unread bytes.
     */
    public function unreadBytes ():int {

        /** @var non-negative-int */
        return Runtime\Str\SB\Inspection::length($this->buffer) - $this->position;

    }

    /**
     * ### Discards unread buffered data
     *
     * Does not change the underlying stream position.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::requireOpen() To check if the buffered reader is open.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed.
     */
    public function clear ():void {

        $this->requireOpen();

        $this->buffer = '';
        $this->position = 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function close ():void {

        if ($this->closed) return;

        $this->buffer = '';
        $this->position = 0;
        $this->closed = true;

    }

    /**
     * ### Fills the internal buffer
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::read() To read from the underlying stream.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::eof() To check if the underlying stream is at EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream cannot make progress.
     */
    private function fill ():void {

        $this->buffer = '';
        $this->position = 0;

        $chunk = $this->stream->read($this->buffer_size);

        if ($chunk === '') {

            if (!$this->stream->eof())
                throw new StreamException(
                    'The stream returned no data before reaching EOF.'
                );

            return;

        }

        $this->buffer = $chunk;

    }

    /**
     * ### Requires an open stream
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\BufferedReader::isClosed() To check if the buffered reader is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The buffered reader is closed.'
            );

    }

}