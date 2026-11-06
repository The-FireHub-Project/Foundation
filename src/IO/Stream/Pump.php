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

namespace FireHub\Foundation\IO\Stream;

use FireHub\Core\Boundary\IO\Stream\ {
    Readable, Writable
};
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Stream Pump
 *
 * Transfers data incrementally between readable and writable streams.
 *
 * Maintains pending data between transfer steps to support partial writes.
 *
 * Each advance operation performs at most one read or one writing.
 *
 * The source and destination remain owned by their original owners.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 */
final class Pump {

    /**
     * ### Pending data
     * @since 1.0.0
     */
    private string $buffer = '';

    /**
     * ### Position within pending data
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $position = 0;

    /**
     * ### Number of transferred bytes
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $transferred = 0;

    /**
     * ### Completion state
     * @since 1.0.0
     */
    private bool $complete;

    /**
     * ### Cancellation state
     * @since 1.0.0
     */
    private bool $cancelled = false;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the source stream is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isWritable() To check if the destination stream is writable.
     * @uses \FireHub\Core\Boundary\IO\Stream::isClosed() To check if the source or destination stream is closed.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $source <p>
     * Source stream.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $destination <p>
     * Destination stream.
     * </p>
     * @param positive-int $buffer_size [optional] <p>
     * Maximum number of bytes requested per read operation.
     * </p>
     * @param null|non-negative-int $limit [optional] <p>
     * Maximum number of bytes to transfer.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the configuration is invalid.
     *
     * @return void
     */
    public function __construct (
        private readonly Readable $source,
        private readonly Writable $destination,
        private readonly int $buffer_size = 65536,
        private readonly ?int $limit = null
    ) {

        if ($buffer_size <= 0)
            throw new StreamException(
                'Buffer size must be greater than zero.'
            );

        if ($limit !== null && $limit < 0)
            throw new StreamException(
                'Transfer limit cannot be negative.'
            );

        if ($source === $destination)
            throw new StreamException(
                'Source and destination cannot be the same stream.'
            );

        if ($source->isClosed() || !$source->isReadable())
            throw new StreamException(
                'The source stream must be open and readable.'
            );

        if ($destination->isClosed() || !$destination->isWritable())
            throw new StreamException(
                'The destination stream must be open and writable.'
            );

        $this->complete = $limit === 0;

    }

    /**
     * ### Advances the transfer
     *
     * Performs at most one underlying read or write operation.
     *
     * Pending data is always written before reading additional data.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Pump::writePending() To write pending data.
     * @uses \FireHub\Foundation\IO\Stream\Pump::pending() To get the number of pending bytes.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isClosed() To check if the source stream is closed.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the source stream is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check if the source stream is at EOF.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read from the source stream.
     * @uses \FireHub\Runtime\Math::min() To clamp the buffer size to the limit.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the read data.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the transfer cannot make progress.
     *
     * @return non-negative-int Number of bytes written during this step.
     */
    public function advance ():int {

        if ($this->cancelled)
            throw new StreamException(
                'The stream pump has been cancelled.'
            );

        if ($this->complete) return 0;

        if ($this->pending() > 0)
            return $this->writePending();

        if ($this->limit !== null && $this->transferred >= $this->limit) {

            $this->complete = true;

            return 0;

        }

        if ($this->source->isClosed() || !$this->source->isReadable())
            throw new StreamException(
                'The source stream is not readable.'
            );

        if ($this->source->eof()) {

            $this->complete = true;

            return 0;

        }

        /** @var non-negative-int $length */
        $length = $this->limit === null
            ? $this->buffer_size
            : Runtime\Math::min(
                $this->buffer_size,
                $this->limit - $this->transferred
            );

        $chunk = $this->source->read($length);

        $received = Runtime\Str\SB\Inspection::length($chunk);

        if ($received > $length)
            throw new StreamException(
                'The source stream returned more bytes than requested.'
            );

        if ($chunk === '') {

            if ($this->source->eof()) { // @phpstan-ignore if.alwaysFalse

                $this->complete = true;

                return 0;

            }

            throw new StreamException(
                'The source stream returned no data before reaching EOF.'
            );

        }

        $this->buffer = $chunk;
        $this->position = 0;

        return 0;

    }

    /**
     * ### Runs the transfer to completion
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Pump::advance() To perform the transfer step.
     * @uses \FireHub\Foundation\IO\Stream\Pump::isComplete() To check if the transfer is complete.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the transfer fails or has been cancelled.
     *
     * @return non-negative-int Total number of transferred bytes.
     */
    public function run ():int {

        while (!$this->isComplete())
            $this->advance();

        return $this->transferred;

    }

    /**
     * ### Determines whether the transfer is complete
     * @since 1.0.0
     *
     * @return bool True if the transfer has completed.
     */
    public function isComplete ():bool {

        return $this->complete;

    }

    /**
     * ### Gets the transferred byte count
     * @since 1.0.0
     *
     * @return non-negative-int Number of bytes written.
     */
    public function transferred ():int {

        return $this->transferred;

    }

    /**
     * ### Gets the number of pending bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the pending data.
     *
     * @return non-negative-int Number of bytes awaiting transfer.
     */
    public function pending ():int {

        /** @var non-negative-int */
        return Runtime\Str\SB\Inspection::length($this->buffer)
            - $this->position;

    }

    /**
     * ### Gets the remaining transfer allowance
     * @since 1.0.0
     *
     * @return null|non-negative-int Remaining transfer allowance.
     */
    public function remaining ():?int {

        if ($this->limit === null) return null;

        /** @var non-negative-int */
        return $this->limit - $this->transferred;

    }

    /**
     * ### Cancels the transfer
     *
     * Discards pending data without closing the underlying streams.
     * @since 1.0.0
     *
     * @return void
     */
    public function cancel ():void {

        if ($this->complete || $this->cancelled) return;

        $this->cancelled = true;

        $this->buffer = '';
        $this->position = 0;

    }

    /**
     * ### Determines whether the transfer was canceled
     * @since 1.0.0
     *
     * @return bool True if the transfer was cancelled.
     */
    public function isCancelled ():bool {

        return $this->cancelled;

    }

    /**
     * ### Writes pending data
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Pump::pending() To get the number of pending bytes.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isClosed() To check if the destination stream is closed.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isWritable() To check if the destination stream is writable.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get the pending data.
     * @uses \FireHub\Core\Boundary\IO\Stream::write() To write to the destination stream.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the written data.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the destination cannot accept data.
     *
     * @return positive-int Number of bytes written.
     */
    private function writePending ():int {

        if ($this->destination->isClosed() || !$this->destination->isWritable())
            throw new StreamException(
                'The destination stream is not writable.'
            );

        $remaining = Runtime\Str\SB\Access::part(
            $this->buffer,
            $this->position
        );

        $written = $this->destination->write($remaining);

        $available = Runtime\Str\SB\Inspection::length($remaining);

        if ($written <= 0 || $written > $available)
            throw new StreamException(
                'The destination stream reported an invalid write length.'
            );

        $this->position += $written;
        $this->transferred += $written;

        if ($this->pending() === 0) {

            $this->buffer = '';
            $this->position = 0;

            if (
                $this->limit !== null
                && $this->transferred >= $this->limit
            ) $this->complete = true;

        }

        return $written;

    }

}