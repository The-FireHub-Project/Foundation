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

use FireHub\Core\Boundary\IO\Stream\Writable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Limited Stream Writer
 *
 * Restricts the total number of bytes accepted by an underlying stream.
 *
 * Writes exceeding the remaining allowance are truncated.
 *
 * Partial writes from the underlying stream are preserved.
 *
 * Once the limit has been reached, write() returns zero.
 *
 * The underlying stream is borrowed and is not automatically closed.
 * @since 1.0.0
 */
final class Writer implements Writable {

    /**
     * ### Number of accepted bytes
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $written = 0;

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
     * @param non-negative-int $limit <p>
     * Maximum number of bytes accepted by this decorator.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the limit is negative.
     *
     * @return void
     */
    public function __construct (
        private readonly Writable $stream,
        private readonly int $limit
    ) {

        if ($limit < 0)
            throw new StreamException(
                'Write limit cannot be negative.'
            );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Writer::isClosed() To check if the writer is closed.
     */
    public function isClosed ():bool {

        return $this->closed || $this->stream->isClosed();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Writer::remaining() To determine the remaining allowance.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::isWritable() To check if the underlying stream is writable.
     * @uses \FireHub\Foundation\IO\Stream\Limit\Writer::isClosed() To check if the writer is closed.
     */
    public function isWritable ():bool {

        return !$this->isClosed()
            && $this->remaining() > 0
            && $this->stream->isWritable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Writer::requireOpen() To check if the writer is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::size() To get the size of the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer is closed.
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
     * @uses \FireHub\Foundation\IO\Stream\Limit\Writer::requireOpen() To check if the writer is open.
     * @uses \FireHub\Foundation\IO\Stream\Limit\Writer::remaining() To determine the remaining allowance.
     * @uses \FireHub\Core\Boundary\IO\Stream\Writable::write() To write data.
     * @uses \FireHub\Runtime\Math::min() To clamp the write length.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the permitted data.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To count bytes.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer is closed or the underlying
     * stream reports an invalid write length.
     */
    public function write (string $data):int {

        $this->requireOpen();

        if ($data === '' || $this->remaining() === 0)
            return 0;

        $length = Runtime\Math::min(
            Runtime\Str\SB\Inspection::length($data),
            $this->remaining()
        );

        $chunk = Runtime\Str\SB\Access::part(
            $data,
            0,
            $length
        );

        $written = $this->stream->write($chunk);

        if ($written < 0 || $written > $length)
            throw new StreamException(
                'The underlying stream reported an invalid write length.'
            );

        $this->written += $written;

        return $written;

    }

    /**
     * ### Gets accepted bytes
     * @since 1.0.0
     *
     * @return non-negative-int Number of bytes accepted by the underlying stream.
     */
    public function written ():int {

        return $this->written;

    }

    /**
     * ### Gets remaining allowance
     * @since 1.0.0
     *
     * @return non-negative-int Number of bytes remaining before reaching the limit.
     */
    public function remaining ():int {

        /** @var non-negative-int */
        return $this->limit - $this->written;

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
     * ### Requires an open writer
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Limit\Writer::isClosed() To check if the writer is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The limited writer is closed.'
            );

    }

}