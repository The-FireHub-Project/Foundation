<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Stream\Tee;

use FireHub\Core\Boundary\IO\Stream\Writable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;
use Throwable;

/**
 * ### Tee Stream Writer
 *
 * Writes the same bytes to multiple underlying writable streams.
 *
 * Every write operation must complete successfully for all destinations. Partial writes are retried until the
 * complete input has been accepted.
 *
 * If a destination fails, the writer enters a permanently failed state because the destinations may no longer
 * contain identical data.
 *
 * This writer does not provide transactional or atomic writes.
 *
 * The underlying streams are borrowed and are not automatically closed.
 * @since 1.0.0
 */
final class Writer implements Writable {

    /**
     * ### Destination streams
     * @since 1.0.0
     *
     * @var non-empty-list<Writable>
     */
    private readonly array $streams;

    /**
     * ### Closed state
     * @since 1.0.0
     */
    private bool $closed = false;

    /**
     * ### Failed state
     * @since 1.0.0
     */
    private bool $failed = false;

    /**
     * ### Number of successfully broadcast bytes
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $written = 0;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::isClosed() To check if the writer is closed.
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::isWritable() To check if the writer is writable.
     * @uses \FireHub\Runtime\ObjectModel\Identity::id() To validate the destination instance.
     * @uses \FireHub\Runtime\Arr\Access::values() To remove duplicate destinations.
     *
     * @param Writable $first <p>
     * First destination stream.
     * </p>
     * @param Writable ...$others <p>
     * Additional destination streams.
     * </p>
     *
     * @throws StreamException If a destination is not writable or the same destination instance is provided more
     * than once.
     *
     * @return void
     */
    public function __construct (Writable $first, Writable ...$others) {

        $streams = [$first, ...$others];

        $seen = [];

        foreach ($streams as $stream) {

            if ($stream->isClosed() || !$stream->isWritable())
                throw new StreamException(
                    'All tee destinations must be open and writable.'
                );

            $id = Runtime\ObjectModel\Identity::id($stream);

            if (isset($seen[$id]))
                throw new StreamException(
                    'The same destination cannot be registered more than once.'
                );

            $seen[$id] = true;

        }

        $this->streams = Runtime\Arr\Access::values($streams); // @phpstan-ignore assign.propertyType

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::isClosed() To check if the writer is closed.
     */
    public function isClosed ():bool {

        if ($this->closed) return true;

        foreach ($this->streams as $stream)
            if ($stream->isClosed()) return true;

        return false;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *.
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::isClosed() To check if the writer is closed.
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::isWritable() To check if any destination is writable.
     */
    public function isWritable ():bool {

        if ($this->failed || $this->isClosed())
            return false;

        foreach ($this->streams as $stream)
            if (!$stream->isWritable()) return false;

        return true;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::requireOpen() To check if the writer is open.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer is closed.
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
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::requireWritable() To check if the writer is writable.
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::writeAll() To write the input to all destinations.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the input data.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If any destination cannot accept the complete
     * input.
     */
    public function write (string $data):int {

        $this->requireWritable();

        $length = Runtime\Str\SB\Inspection::length($data);

        if ($length === 0) return 0;

        try {

            foreach ($this->streams as $stream)
                $this->writeAll($stream, $data, $length);

        } catch (Throwable $exception) {

            $this->failed = true;

            throw new StreamException(
                'Tee write failed. Destinations may contain different amounts of data.',
                previous: $exception
            );

        }

        $this->written += $length;

        return $length;

    }

    /**
     * ### Gets successfully broadcast bytes
     *
     * Counts bytes from operations completed by every destination. Failed operations are not included.
     * @since 1.0.0
     *
     * @return non-negative-int Number of successfully broadcast bytes.
     */
    public function written ():int {

        return $this->written;

    }

    /**
     * ### Checks whether the writer has failed
     * @since 1.0.0
     *
     * @return bool True if a previous write failed.
     */
    public function isFailed ():bool {

        return $this->failed;

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
     * ### Writes all bytes to one destination
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Writable::write() To write to the destination.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get a part of the input data.
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Writable $stream <p>
     * Destination stream.
     * </p>
     *
     * @param string $data <p>
     * Input data.
     * </p>
     *
     * @param positive-int $length <p>
     * Input length.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the destination stops accepting data or
     * reports an invalid write length.
     *
     * @return void
     */
    private function writeAll (Writable $stream, string $data, int $length):void {

        $offset = 0;

        while ($offset < $length) {

            $remaining = Runtime\Str\SB\Access::part(
                $data,
                $offset
            );

            $written = $stream->write($remaining);

            $remaining_length = $length - $offset;

            if ($written <= 0 || $written > $remaining_length)
                throw new StreamException(
                    'The destination could not accept the remaining data.'
                );

            $offset += $written;

        }

    }

    /**
     * ### Requires an open writer
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::isClosed() To check if the writer is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer is closed.
     *
     * @return void
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The tee writer is closed.'
            );

    }

    /**
     * ### Requires a writable state
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Tee\Writer::isWritable() To check if any destination is writable.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the writer has failed or any destination is
     * unavailable.
     *
     * @return void
     */
    private function requireWritable ():void {

        $this->requireOpen();

        if ($this->failed)
            throw new StreamException(
                'The tee writer is in a failed state.'
            );

        if (!$this->isWritable())
            throw new StreamException(
                'One or more tee destinations are not writable.'
            );

    }

}