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

use FireHub\Core\Boundary\IO\Stream\Writable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;
use Closure;

/**
 * ### Progress Stream Writer
 *
 * Tracks the number of bytes successfully accepted by an underlying writable stream.
 *
 * Supports partial writes and optional progress notifications.
 *
 * The underlying stream is borrowed and is not automatically closed.
 * @since 1.0.0
 */
final class Writer implements Writable {

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
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $stream <p>
     * Underlying writable stream.
     * </p>
     * @param null|non-negative-int $total [optional] <p>
     * Expected total number of bytes.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If total is negative.
     *
     * @return void
     */
    public function __construct (
        private readonly Writable $stream,
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
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isClosed() To check if the underlying stream is closed.
     */
    public function isClosed ():bool {

        return $this->closed || $this->stream->isClosed();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isWritable() To check if the underlying stream is writable.
     * @uses \FireHub\Foundation\IO\Stream\Progress\Writer::isClosed() To check if the writer is closed.
     */
    public function isWritable ():bool {

        return !$this->isClosed() && $this->stream->isWritable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Progress\Writer::requireOpen() To check if the writer is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::size() To get the size of the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer is closed.
     *
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
     * @uses \FireHub\Foundation\IO\Stream\Progress\Writer::requireOpen() To check if the writer is open.
     * @uses \FireHub\Foundation\IO\Stream\Progress\Writer::write() To write to the underlying stream.
     * @uses \FireHub\Foundation\IO\Stream\Progress\Writer::notify() To notify the progress callback.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream cannot make progress.
     */
    public function write (string $data):int {

        $this->requireOpen();

        if ($data === '') return 0;

        $written = $this->stream->write($data);

        $length = Runtime\Str\SB\Inspection::length($data);

        if ($written < 0 || $written > $length)
            throw new StreamException(
                'The underlying stream reported an invalid write length.'
            );

        if ($written > 0) {

            $this->processed += $written;

            $this->notify();

        }

        return $written;

    }

    /**
     * ### Gets processed bytes
     * @since 1.0.0
     *
     * @return non-negative-int Number of successfully written bytes.
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
     * @uses \FireHub\Runtime\Math::max() To prevent negative values.
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
     * @since 1.0.0
     *
     * @param Closure(int, ?int):void $callback
     * Progress callback.
     *
     * @return self Current writer.
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
     * ### Requires an open writer
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Progress\Writer::isClosed() To check if the writer is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The progress writer is closed.'
            );

    }

}