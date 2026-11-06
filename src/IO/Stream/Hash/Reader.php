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

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\Hash\Context;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime\Hash\Algorithm;
use FireHub\Runtime;

/**
 * ### Hashing Stream Reader
 *
 * Calculates an incremental hash of bytes successfully read from an underlying stream.
 *
 * Only bytes returned to the caller are included in the digest.
 *
 * The underlying stream is borrowed and is not automatically closed.
 * @since 1.0.0
 */
final class Reader implements Readable {

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
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Underlying readable stream.
     * </p>
     *
     * @param \FireHub\Runtime\Hash\Algorithm $algorithm [optional] <p>
     * Hashing algorithm.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private readonly Readable $stream,
        Algorithm $algorithm = Algorithm::SHA256
    ) {

        $this->context = new Context($algorithm);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::isClosed() To check if the reader is closed.
     */
    public function isClosed ():bool {

        return $this->closed || $this->stream->isClosed();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::isClosed() To check if the reader is closed.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the underlying stream is readable.
     */
    public function isReadable ():bool {

        return !$this->isClosed() && $this->stream->isReadable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::requireOpen() To check if the reader is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::size() To get the size of the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
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
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::requireOpen() To check if the reader is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read from the underlying stream.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check if the underlying stream is at EOF.
     * @uses \FireHub\Foundation\Hash\Context::update() To update the hash context.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the read data.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream returns more bytes than
     * requested, or returns no data before reaching EOF.
     */
    public function read (int $length):string {

        $this->requireOpen();

        if ($length < 0)
            throw new StreamException(
                'Read length cannot be negative.'
            );

        if ($length === 0) return '';

        $data = $this->stream->read($length);

        if (Runtime\Str\SB\Inspection::length($data) > $length)
            throw new StreamException(
                'The underlying stream returned more bytes than requested.'
            );

        if ($data === '' && !$this->stream->eof())
            throw new StreamException(
                'The underlying stream returned no data before reaching EOF.'
            );

        $this->context->update($data);

        return $data;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::requireOpen() To check if the reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::read() To read from the underlying stream.
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::eof() To check if the underlying stream is at EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream cannot make progress.
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
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::requireOpen() To check if the reader is open.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     */
    public function eof ():bool {

        $this->requireOpen();

        return $this->stream->eof();

    }

    /**
     * ### Gets the current hash digest
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Hash\Context::digest() To get the current hash digest.
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
     * @uses \FireHub\Foundation\Hash\Context::bytes() To get the number of hashed bytes.
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
     * ### Requires an open reader
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Hash\Reader::isClosed() To check if the reader is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The hashing reader is closed.'
            );

    }

}