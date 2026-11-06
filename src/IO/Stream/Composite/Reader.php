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

namespace FireHub\Foundation\IO\Stream\Composite;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Composite Stream Reader
 *
 * Combines multiple readable streams into a single sequential stream.
 *
 * Reads data from each source in the order provided, automatically advancing to the next source when the current
 * source reaches EOF.
 *
 * A single read operation may consume bytes from multiple sources.
 *
 * The underlying streams are borrowed and are not automatically closed.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 */
final class Reader implements Readable {

    /**
     * ### Source streams
     * @since 1.0.0
     *
     * @var list<\FireHub\Core\Boundary\IO\Stream\Readable>
     */
    private readonly array $streams;

    /**
     * ### Current source index
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $index = 0;

    /**
     * ### Closed state
     * @since 1.0.0
     */
    private bool $closed = false;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To remove keys from the array.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable ...$streams <p>
     * Source streams to concatenate in the given order.
     * </p>
     *
     * @return void
     */
    public function __construct (Readable ...$streams) {

        $this->streams = Runtime\Arr\Access::values($streams);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isClosed ():bool {

        return $this->closed;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the stream is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isClosed() To check if the stream is closed.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of streams.
     */
    public function isReadable ():bool {

        if ($this->closed) return false;

        for (
            $index = $this->index, $index_max = Runtime\Arr\Inspection::count($this->streams);
            $index < $index_max;
            $index++
        ) {

            $stream = $this->streams[$index]; // @phpstan-ignore offsetAccess.notFound

            if ($stream->isClosed() || !$stream->isReadable())
                return false;

        }

        return true;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Composite\Reader::requireOpen() To ensure the reader is open.
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
     * @uses \FireHub\Foundation\IO\Stream\Composite\Reader::requireOpen() To ensure the reader is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check if the stream is at EOF.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the stream is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isClosed() To check if the stream is closed.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of streams.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the read data.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract the read data.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::read() To read from the stream.
     * @uses \FireHub\Runtime\Math::min() To clamp the read length.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of streams.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To count the received bytes.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the length is negative, the reader is
     * closed, or an underlying source violates the read contract.
     */
    public function read (int $length):string {

        $this->requireOpen();

        if ($length < 0)
            throw new StreamException(
                'Read length cannot be negative.'
            );

        if ($length === 0) return '';

        $result = '';

        while (
            Runtime\Str\SB\Inspection::length($result) < $length
            && $this->index < count($this->streams)
        ) {

            $stream = $this->streams[$this->index];

            if ($stream->isClosed() || !$stream->isReadable())
                throw new StreamException(
                    'The current composite source is not readable.'
                );

            if ($stream->eof()) {

                $this->index++;

                continue;

            }

            /** @var non-negative-int $remaining */
            $remaining = $length - Runtime\Str\SB\Inspection::length($result);

            $chunk = $stream->read($remaining);

            $received = Runtime\Str\SB\Inspection::length($chunk);

            if ($received > $remaining)
                throw new StreamException(
                    'The underlying stream returned more bytes than requested.'
                );

            if ($chunk === '') {

                if ($stream->eof()) { // @phpstan-ignore if.alwaysFalse

                    $this->index++;

                    continue;

                }

                throw new StreamException(
                    'The underlying stream returned no data before reaching EOF.'
                );

            }

            $result .= $chunk;

            if ($stream->eof()) // @phpstan-ignore if.alwaysFalse
                $this->index++;

        }

        return $result;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Composite\Reader::requireOpen() To ensure the reader is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check if the stream is at EOF.
     * @uses \FireHub\Foundation\IO\Stream\Composite\Reader::read() To read from the stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     */
    public function contents ():string {

        $this->requireOpen();

        $contents = '';

        while (!$this->eof()) {

            $chunk = $this->read(8192);

            if ($chunk === '') {

                if ($this->eof()) break; // @phpstan-ignore if.alwaysFalse

                throw new StreamException(
                    'The composite stream returned no data before reaching EOF.'
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
     * @uses \FireHub\Foundation\IO\Stream\Composite\Reader::requireOpen() To ensure the reader is open.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::eof() To check if the stream is at EOF.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isReadable() To check if the stream is readable.
     * @uses \FireHub\Core\Boundary\IO\Stream\Readable::isClosed() To check if the stream is closed.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of streams.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the reader is closed or an underlying
     * source violates the read contract.
     */
    public function eof ():bool {

        $this->requireOpen();

        while ($this->index < Runtime\Arr\Inspection::count($this->streams)) {

            $stream = $this->streams[$this->index]; // @phpstan-ignore offsetAccess.notFound

            if ($stream->isClosed() || !$stream->isReadable())
                throw new StreamException(
                    'The current composite source is not readable.'
                );

            if (!$stream->eof()) return false;

            $this->index++;

        }

        return true;

    }

    /**
     * ### Gets the current source index
     * @since 1.0.0
     *
     * @return non-negative-int Current source index.
     */
    public function index ():int {

        return $this->index;

    }

    /**
     * ### Gets the number of source streams
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of streams.
     *
     * @return non-negative-int Number of source streams.
     */
    public function count ():int {

        return Runtime\Arr\Inspection::count($this->streams);

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
     * @uses \FireHub\Foundation\IO\Stream\Composite\Reader::isClosed() To check if the reader is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the composite reader is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->closed)
            throw new StreamException(
                'The composite reader is closed.'
            );

    }

}