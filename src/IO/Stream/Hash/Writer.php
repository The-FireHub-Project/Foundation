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

namespace FireHub\Foundation\IO\Stream\Hash;

use FireHub\Core\Boundary\IO\Stream\Writable;
use FireHub\Foundation\Hash\Context;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime\Hash\Algorithm;
use FireHub\Runtime;

/**
 * ### Hashing Stream Writer
 *
 * Calculates an incremental hash of bytes successfully accepted by an underlying writable stream.
 *
 * Supports partial writes by hashing only the bytes reported as written.
 *
 * The underlying stream is borrowed and is not automatically closed.
 * @since 1.0.0
 */
final class Writer implements Writable {

    /**
     * ### Hash context
     * @since 1.0.0
     */
    private readonly Context $context;

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
     *
     * @param \FireHub\Runtime\Hash\Algorithm $algorithm [optional] <p>
     * Hashing algorithm.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private readonly Writable $stream,
        Algorithm $algorithm = Algorithm::SHA256
    ) {

        $this->context = new Context($algorithm);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Hash\Writer::isClosed() To check if the writer is closed.
     */
    public function isClosed ():bool {

        return $this->closed || $this->stream->isClosed();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Hash\Writer::isClosed() To check if the writer is closed.
     * @uses \FireHub\Foundation\IO\Stream\Hash\Writer::isWritable() To check if the underlying stream is writable.
     */
    public function isWritable ():bool {

        return !$this->isClosed() && $this->stream->isWritable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Hash\Writer::requireOpen() To check if the writer is open.
     * @uses \FireHub\Foundation\IO\Stream\Hash\Writer::size() To get the underlying stream size.
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
     * @uses \FireHub\Foundation\IO\Stream\Hash\Writer::requireOpen() To check if the writer is open.
     * @uses \FireHub\Foundation\IO\Stream\Hash\Writer::write() To write to the underlying stream.
     * @uses \FireHub\Foundation\Hash\Context::update() To update the hash context.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the data.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the written data.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream reports an invalid.
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

            $this->context->update(
                Runtime\Str\SB\Access::part(
                    $data,
                    0,
                    $written
                )
            );

        }

        return $written;

    }

    /**
     * ### Gets the current hash digest
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Hash::digest() To get the hash digest.
     *
     * @param bool $binary [optional] <p>
     * Whether to return raw binary data.
     * </p>
     *
     * @return non-empty-string Hash digest.
     */
    public function digest (bool $binary = false):string {

        return $this->context->digest($binary);

    }

    /**
     * ### Gets the number of hashed bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Hash::bytes() To get the number of hashed bytes.
     *
     * @return non-negative-int Number of hashed bytes.
     */
    public function bytes ():int {

        return $this->context->bytes();

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
     * @uses \FireHub\Foundation\IO\Stream\Hash\Writer::isClosed() To check if the writer is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The hashing writer is closed.'
            );

    }

}