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

namespace FireHub\Foundation\IO\Stream\Progress;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;
use Closure;

/**
 * ### Progress Stream Reader
 *
 * Tracks the number of bytes successfully read from an underlying stream.
 *
 * Supports optional progress notifications and total-size tracking.
 *
 * Only bytes returned by successful read operations are counted.
 *
 * The underlying stream is borrowed and is not automatically closed.
 * @since 1.0.0
 */
final class Reader implements Readable {

    /**
     * ### Processed bytes
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $processed = 0;

    /**
     * ### Progress callback
     * @since 1.0.0
     *
     * @var null|Closure(int, ?int):void
     */
    private ?Closure $callback = null;

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
     * @param null|non-negative-int $total [optional] <p>
     * Expected total number of bytes. Null means unknown.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If total is negative.
     *
     * @return void
     */
    public function __construct (
        private readonly Readable $stream,
        private readonly ?int $total = null
    ) {

        if ($total !== null && $total < 0)
            throw new StreamException(
                'Total bytes cannot be negative.'
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
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the underlying stream is readable.
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::isClosed() To check if the reader is closed.
     */
    public function isReadable ():bool {

        return !$this->isClosed() && $this->stream->isReadable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::requireOpen() To ensure the stream is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::size() To get the underlying stream size.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
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
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::requireOpen() To ensure the stream is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read from the underlying stream.
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::notify() To notify the progress callback.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the returned data.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed, or the underlying stream
     * returns no data before reaching EOF.
     */
    public function read (int $length):string {

        $this->requireOpen();

        if ($length < 0)
            throw new StreamException(
                'Read length cannot be negative.'
            );

        if ($length === 0) return '';

        $data = $this->stream->read($length);

        $received = Runtime\Str\SB\Inspection::length($data);

        if ($received > $length)
            throw new StreamException(
                'The underlying stream returned more bytes than requested.'
            );

        if ($data === '' && !$this->stream->eof())
            throw new StreamException(
                'The underlying stream returned no data before reaching EOF.'
            );

        if ($received > 0) {

            $this->processed += $received;

            $this->notify();

        }

        return $data;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::requireOpen() To ensure the stream is open.
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::read() To read the stream.
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::eof() To check if EOF is reached.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
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
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::requireOpen() To ensure the stream is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check if the underlying stream is at EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function eof ():bool {

        $this->requireOpen();

        return $this->stream->eof();

    }

    /**
     * ### Gets processed bytes
     * @since 1.0.0
     *
     * @return non-negative-int Number of successfully read bytes.
     */
    public function processed ():int {

        return $this->processed;

    }

    /**
     * ### Gets expected total bytes
     * @since 1.0.0
     *
     * @return null|non-negative-int Expected total bytes.
     */
    public function total ():?int {

        return $this->total;

    }

    /**
     * ### Gets remaining bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::max() To ensure the result is non-negative.
     *
     * @return null|non-negative-int Remaining expected bytes.
     */
    public function remaining ():?int {

        if ($this->total === null) return null;

        /** @var non-negative-int */
        return Runtime\Math::max(
            0,
            $this->total - $this->processed
        );

    }

    /**
     * ### Gets progress percentage
     * @since 1.0.0
     *
     * @return null|float Progress percentage, or null if total is unknown.
     */
    public function percentage ():?float {

        if ($this->total === null) return null;

        if ($this->total === 0)
            return $this->processed === 0 ? 100.0 : null;

        return $this->processed / $this->total * 100;

    }

    /**
     * ### Registers a progress callback
     *
     * The callback receives the number of processed bytes and the expected total number of bytes.
     * @since 1.0.0
     *
     * @param Closure(int, ?int):void $callback
     * Progress callback.
     *
     * @return self Current reader.
     */
    public function onProgress (Closure $callback):self {

        $this->callback = $callback;

        return $this;

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
     * ### Notifies the progress callback
     * @since 1.0.0
     *
     * @return void
     */
    private function notify ():void {

        if ($this->callback !== null)
            ($this->callback)($this->processed, $this->total);

    }

    /**
     * ### Requires an open reader
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Progress\Reader::isClosed() To check if the reader is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The progress reader is closed.'
            );

    }

}