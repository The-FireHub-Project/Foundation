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

namespace FireHub\Foundation\IO\Stream\Limit;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Limited Stream Reader
 *
 * Restricts the number of bytes that can be read from an underlying stream.
 *
 * The limit applies from the stream's current position.
 *
 * Reaching the configured limit is treated as EOF, even when the underlying stream contains additional data.
 *
 * The underlying stream is borrowed and is not automatically closed.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 */
final class Reader implements Readable {

    /**
     * ### Number of bytes consumed
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $consumed = 0;

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
     * @param non-negative-int $limit <p>
     * Maximum number of bytes that may be consumed.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the limit is negative.
     *
     * @return void
     */
    public function __construct (
        private readonly Readable $stream,
        private readonly int $limit
    ) {

        if ($limit < 0)
            throw new StreamException(
                'Read limit cannot be negative.'
            );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::isClosed() To check if the reader is closed.
     */
    public function isClosed ():bool {

        return $this->closed || $this->stream->isClosed();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::isClosed() To check if the reader is closed.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the underlying stream is readable.
     */
    public function isReadable ():bool {

        return !$this->isClosed()
            && $this->stream->isReadable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::requireOpen() To check if the reader is open.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     */
    public function size ():?int {

        $this->requireOpen();

        return null;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::requireOpen() To check if the reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::remaining() To determine the remaining allowance.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read from the underlying stream.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check for EOF.
     * @uses \FireHub\Runtime\Math::min() To clamp the requested length.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To count the received bytes.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the length is invalid, the reader is
     * closed, or the underlying stream violates the read contract.
     */
    public function read (int $length):string {

        $this->requireOpen();

        if ($length < 0)
            throw new StreamException(
                'Read length cannot be negative.'
            );

        if ($length === 0 || $this->remaining() === 0)
            return '';

        $requested = Runtime\Math::min(
            $length,
            $this->remaining()
        );

        $chunk = $this->stream->read($requested);

        $received = Runtime\Str\SB\Inspection::length($chunk);

        if ($received > $requested)
            throw new StreamException(
                'The underlying stream returned more bytes than requested.'
            );

        if ($chunk === '' && !$this->stream->eof())
            throw new StreamException(
                'The underlying stream returned no data before reaching EOF.'
            );

        $this->consumed += $received;

        return $chunk;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::requireOpen() To check if the reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::eof() To check for EOF.
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::read() To read the next chunk.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     */
    public function contents ():string {

        $this->requireOpen();

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
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::requireOpen() To check if the reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::remaining() To check the remaining allowance.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     */
    public function eof ():bool {

        $this->requireOpen();

        return $this->remaining() === 0 || $this->stream->eof();

    }

    /**
     * ### Gets consumed bytes
     * @since 1.0.0
     *
     * @return non-negative-int Number of bytes consumed through this decorator.
     */
    public function consumed ():int {

        return $this->consumed;

    }

    /**
     * ### Gets remaining allowance
     * @since 1.0.0
     *
     * @return non-negative-int Number of bytes remaining before reaching the limit.
     */
    public function remaining ():int {

        /** @var non-negative-int */
        return $this->limit - $this->consumed;

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
     * @uses \FireHub\Foundation\IO\Stream\Limit\Reader::isClosed() To check if the reader is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The limited reader is closed.'
            );

    }

}