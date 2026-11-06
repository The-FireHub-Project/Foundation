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

use FireHub\Core\Boundary\IO\Stream\Writable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Buffered Writer
 *
 * Provides buffered writing for an existing writable stream.
 *
 * Accumulates small writes in memory and forwards them to the underlying stream when the buffer reaches its
 * configured capacity.
 *
 * Pending data can be explicitly forwarded using flush().
 *
 * The underlying stream remains owned by its original owner.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 */
final class Writer implements Writable {

    /**
     * ### Pending write buffer
     * @since 1.0.0
     */
    private string $buffer = '';

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
     * @param positive-int $buffer_size [optional] <p>
     * Maximum buffered bytes before automatic flushing.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffer size is invalid.
     *
     * @return void
     */
    public function __construct (
        private readonly Writable $stream,
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::isWritable() To check if the underlying stream is writable.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::isClosed() To check if the buffered reader is closed.
     */
    public function isWritable ():bool {

        return !$this->isClosed() && $this->stream->isWritable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::size() To get the underlying stream size.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered writer is closed.
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
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::flush() To flush pending buffered data.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::writeDirect() To write data directly to the underlying stream.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::isWritable() To check if the underlying stream is writable.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered writer is closed or the
     * underlying stream is not writable.
     */
    public function write (string $data):int {

        $this->requireOpen();

        if (!$this->stream->isWritable())
            throw new StreamException(
                'The underlying stream is not writable.'
            );

        $length = Runtime\Str\SB\Inspection::length($data);

        if ($length === 0) return 0;

        $pending = Runtime\Str\SB\Inspection::length($this->buffer);

        if ($pending + $length < $this->buffer_size) {

            $this->buffer .= $data;

            return $length;

        }

        if ($pending > 0) $this->flush();

        if ($length >= $this->buffer_size) {

            $this->writeDirect($data);

            return $length;

        }

        $this->buffer = $data;

        return $length;

    }

    /**
     * ### Flushes pending buffered data
     *
     * Writes all pending bytes to the underlying stream.
     *
     * Handles partial writes by continuing until all bytes are forwarded.
     *
     * If writing fails, bytes that have not been successfully written remain in the buffer.
     *
     * This method does not guarantee physical disk synchronization.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::write() To write data to the underlying stream.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get a part of the string.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream cannot make progress.
     *
     * @return void
     */
    public function flush ():void {

        $this->requireOpen();

        while ($this->buffer !== '') {

            $written = $this->stream->write($this->buffer);

            if ($written <= 0)
                throw new StreamException(
                    'The underlying stream could not accept buffered data.'
                );

            if ($written > Runtime\Str\SB\Inspection::length($this->buffer))
                throw new StreamException(
                    'The underlying stream reported an invalid write length.'
                );

            $this->buffer = Runtime\Str\SB\Access::part(
                $this->buffer,
                $written
            );

        }

    }

    /**
     * ### Gets the number of pending buffered bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     *
     * @return non-negative-int Pending bytes.
     */
    public function pendingBytes ():int {

        return Runtime\Str\SB\Inspection::length($this->buffer);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::flush() To flush pending buffered data.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream cannot make progress.
     */
    public function close ():void {

        if ($this->closed) return;

        $this->flush();

        $this->closed = true;

    }

    /**
     * ### Requires an open stream
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::isClosed() To check if the buffered writer is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered writer is closed.
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The buffered writer is closed.'
            );

    }

    /**
     * ### Writes data directly to the underlying stream
     *
     * Continues writing until all bytes have been transferred.
     *
     * Handles partial writes without discarding unwritten data.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::write() To write data to the underlying stream.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get a part of the string.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     *
     * @param string $data <p>
     * Data to write.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream cannot make progress.
     *
     * @return void
     */
    private function writeDirect (string $data):void {

        $length = Runtime\Str\SB\Inspection::length($data);

        $offset = 0;

        while ($offset < $length) {

            $written = $this->stream->write(
                Runtime\Str\SB\Access::part($data, $offset)
            );

            if ($written <= 0)
                throw new StreamException(
                    'The underlying stream could not accept the remaining data.'
                );

            if ($written > $length - $offset)
                throw new StreamException(
                    'The underlying stream reported an invalid write length.'
                );

            $offset += $written;

        }

    }

}