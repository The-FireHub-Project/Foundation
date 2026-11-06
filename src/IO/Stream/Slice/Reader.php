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

namespace FireHub\Foundation\IO\Stream\Slice;

use FireHub\Core\Boundary\IO\Stream\ {
    Readable, Seekable, Positionable
};
use FireHub\Core\Type\IO\SeekOrigin;
use FireHub\Core\Foundation\Constant\Numeric\IntegerLimits;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Sliced Stream Reader
 *
 * Provides a seekable, read-only view over a fixed byte range of an underlying readable and seekable stream.
 *
 * Maintains an independent logical position within the slice.
 *
 * Reading repositions the underlying stream to the corresponding absolute offset.
 *
 * The underlying stream is borrowed and is not automatically closed.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 */
final class Reader implements Readable, Seekable, Positionable {

    /**
     * ### Current logical position
     * @since 1.0.0
     *
     * @var non-negative-int
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
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the underlying stream is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Seekable::isSeekable() To check if the underlying stream is seekable.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To check if the slice range exceeds the
     * supported integer range.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable&\FireHub\Core\Boundary\IO\Stream\Seekable $stream <p>
     * Underlying readable and seekable stream.
     * </p>
     * @param non-negative-int $offset <p>
     * Absolute starting byte offset.
     * </p>
     * @param non-negative-int $length <p>
     * Maximum number of bytes exposed by the slice.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the range is invalid or the underlying
     * stream does not support reading and seeking.
     *
     * @return void
     */
    public function __construct (
        private readonly Readable&Seekable $stream,
        private readonly int $offset,
        private readonly int $length
    ) {

        if ($offset < 0 || $length < 0)
            throw new StreamException(
                'Slice offset and length cannot be negative.'
            );

        if ($offset > IntegerLimits::MAX - $length)
            throw new StreamException(
                'Slice range exceeds the supported integer range.'
            );

        if (
            $stream->isClosed()
            || !$stream->isReadable()
            || !$stream->isSeekable()
        ) throw new StreamException(
            'The underlying stream must be open, readable, and seekable.'
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isClosed() To check if the underlying stream is closed.
     */
    public function isClosed ():bool {

        return $this->closed || $this->stream->isClosed();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::isClosed() To check if the reader is closed.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the underlying stream is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Seekable::isSeekable() To check if the underlying stream is seekable.
     */
    public function isReadable ():bool {

        return !$this->isClosed()
            && $this->stream->isReadable()
            && $this->stream->isSeekable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::isClosed() To check if the reader is closed.
     * @uses \FireHub\Core\Boundary\IO\Stream\Seekable::isSeekable() To check if the underlying stream is seekable.
     */
    public function isSeekable ():bool {

        return !$this->isClosed() && $this->stream->isSeekable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::requireOpen() To ensure the reader is open.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     */
    public function size ():int {

        $this->requireOpen();

        return $this->length;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::requireReadable() To ensure the reader is readable.
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::eof() To check if the slice is at EOF.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read from the underlying stream.
     * @uses \FireHub\Runtime\Math::min() To clamp the requested length.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To count the received bytes.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails or the requested length is
     * invalid.
     */
    public function read (int $length):string {

        $this->requireReadable();

        if ($length < 0)
            throw new StreamException(
                'Read length cannot be negative.'
            );

        if ($length === 0 || $this->eof()) return '';

        $requested = Runtime\Math::min(
            $length,
            $this->remaining()
        );

        $this->stream->seek(
            $this->offset + $this->position
        );

        $data = $this->stream->read($requested);

        $received = Runtime\Str\SB\Inspection::length($data);

        if ($received > $requested)
            throw new StreamException(
                'The underlying stream returned more bytes than requested.'
            );

        if ($data === '' && !$this->stream->eof())
            throw new StreamException(
                'The underlying stream returned no data before reaching EOF.'
            );

        $this->position += $received;

        return $data;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::requireReadable() To ensure the reader is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check if the slice is at EOF.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read from the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     */
    public function contents ():string {

        $this->requireReadable();

        $contents = '';

        while (!$this->eof()) {

            $chunk = $this->read(8192);

            if ($chunk === '') break;

            $contents .= $chunk;

        }

        return $contents;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::requireOpen() To ensure the reader is open.
     *
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     */
    public function eof ():bool {

        $this->requireOpen();

        return $this->position >= $this->length;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::requireOpen() To ensure the reader is open.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     */
    public function tell ():int {

        $this->requireOpen();

        return $this->position;

    }

    /**
     * ### Moves the current position within the slice
     *
     * Changes the logical position relative to the beginning, current position, or end of the slice.
     *
     * The underlying stream position is not modified until the next read.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::requireOpen() To ensure the reader is open.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To check if the resulting position exceeds
     *
     * @param int $offset <p>
     * Position offset in bytes.
     * </p>
     * @param \FireHub\Core\Type\IO\SeekOrigin $origin [optional] <p>
     * Reference position used to calculate the new position.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed, the resulting
     * position exceeds the supported integer range, or the position is outside the slice boundaries.
     *
     * @return void
     */
    public function seek (int $offset, SeekOrigin $origin = SeekOrigin::START):void {

        $this->requireOpen();

        $base = match ($origin) {

            SeekOrigin::START => 0,
            SeekOrigin::CURRENT => $this->position,
            SeekOrigin::END => $this->length

        };

        if ($offset > 0 && $base > IntegerLimits::MAX - $offset)
            throw new StreamException(
                'Slice seek position exceeds the supported integer range.'
            );

        if ($offset < -$base || $offset > $this->length - $base)
            throw new StreamException(
                'Cannot seek outside the slice boundaries.'
            );

        $this->position = $base + $offset; // @phpstan-ignore assign.propertyType

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::seek() To move the position to the beginning of the slice.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If seeking fails.
     */
    public function rewind ():void {

        $this->seek(0);

    }

    /**
     * ### Gets the slice starting offset
     * @since 1.0.0
     *
     * @return non-negative-int Absolute starting byte offset.
     */
    public function offset ():int {

        return $this->offset;

    }

    /**
     * ### Gets the number of remaining bytes
     * @since 1.0.0
     *
     * @return non-negative-int Remaining bytes within the slice.
     */
    public function remaining ():int {

        /** @var non-negative-int */
        return $this->length - $this->position;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function close ():void {

        $this->closed = true;

    }

    /**
     * ### Requires an open reader
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::isClosed() To check if the reader is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The sliced reader is closed.'
            );

    }

    /**
     * ### Requires a readable stream
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::requireOpen() To check if the reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Slice\Reader::isReadable() To check if the underlying stream is readable.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream is not readable or
     * seekable.
     *
     * @return void
     */
    private function requireReadable ():void {

        $this->requireOpen();

        if (!$this->isReadable())
            throw new StreamException(
                'The sliced reader is not readable.'
            );

    }

}