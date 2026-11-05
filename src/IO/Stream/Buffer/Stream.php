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

use FireHub\Core\Boundary\IO\Stream\ {
    Readable, Writable, Seekable
};
use FireHub\Core\Type\IO\SeekOrigin;
use FireHub\Foundation\IO\Stream\Exception\StreamException;

/**
 * ### Buffered Stream
 *
 * Combines buffered reading and writing for a duplex stream.
 *
 * Coordinates transitions between reading and writing to preserve the logical stream position.
 *
 * Switching from reading to writing requires seeking when unread prefetched bytes remain.
 *
 * The underlying stream is not automatically closed.
 *
 * This implementation is intended for blocking streams.
 * @since 1.0.0
 *
 * @phpstan-type IncomingStream = (\FireHub\Core\Boundary\IO\Stream\Readable&\FireHub\Core\Boundary\IO\Stream\Writable)
 */
final class Stream implements Readable, Writable {

    /**
     * ### Buffered reader
     * @since 1.0.0
     */
    private readonly Reader $reader;

    /**
     * ### Buffered writer
     * @since 1.0.0
     */
    private readonly Writer $writer;

    /**
     * ### Closed state
     * @since 1.0.0
     */
    private bool $closed = false;

    /**
     * ### Last operation was reading
     * @since 1.0.0
     */
    private bool $reading = false;

    /**
     * ### Last operation was writing
     * @since 1.0.0
     */
    private bool $writing = false;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param IncomingStream $stream <p>
     * Underlying readable and writable stream.
     * </p>
     * @param positive-int $read_buffer_size [optional] <p>
     * Read buffer capacity.
     * </p>
     * @param positive-int $write_buffer_size [optional] <p>
     * rite buffer capacity.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffer size is invalid.
     *
     * @return void
     */
    public function __construct (
        private readonly Readable&Writable $stream,
        int $read_buffer_size = 8192,
        int $write_buffer_size = 8192
    ) {

        $this->reader = new Reader(
            $stream,
            $read_buffer_size
        );

        $this->writer = new Writer(
            $stream,
            $write_buffer_size
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses IncomingStream::isClosed() To check if the buffered reader is closed.
     */
    public function isClosed ():bool {

        return $this->closed || $this->stream->isClosed();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses IncomingStream::isClosed() To check if the buffered reader is closed.
     * @uses IncomingStream::isReadable() To check if the underlying stream is readable.
     */
    public function isReadable ():bool {

        return !$this->isClosed() && $this->stream->isReadable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses IncomingStream::isClosed() To check if the buffered reader is closed.
     * @uses IncomingStream::isWritable() To check if the underlying stream is writable.
     */
    public function isWritable ():bool {

        return !$this->isClosed() && $this->stream->isWritable();

    }

    /**
     * @inheritDoc
     *
     * @uses IncomingStream::size() To get the size of the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed.
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
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::prepareRead() To prepare the stream for reading.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::read() To read from the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed, or the stream
     * returns no data before reaching EOF.
     */
    public function read (int $length):string {

        $this->requireOpen();

        if ($length < 0)
            throw new StreamException(
                'Read length cannot be negative.'
            );

        if ($length === 0) return '';

        $this->prepareRead();

        return $this->reader->read($length);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::prepareRead() To prepare the stream for reading.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::contents() To read the entire contents of the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed.
     */
    public function contents ():string {

        $this->requireOpen();

        $this->prepareRead();

        return $this->reader->contents();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::prepareRead() To prepare the stream for reading.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::eof() To check if the underlying stream has reached EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed.
     */
    public function eof ():bool {

        $this->requireOpen();

        if ($this->writing)
            $this->prepareRead();

        return $this->reader->eof();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::requireOpen() To check if the buffered reader is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::prepareWrite() To prepare the stream for writing.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::write() To write to the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered writer is closed or the underlying
     * stream cannot make progress.
     */
    public function write (string $data):int {

        $this->requireOpen();

        if ($data === '') return 0;

        $this->prepareWrite();

        return $this->writer->write($data);

    }

    /**
     * ### Flushes pending writes
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::requireOpen() To check if the buffered writer is open.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::flush() To flush the underlying stream.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered writer is closed.
     */
    public function flush ():void {

        $this->requireOpen();

        $this->writer->flush();

    }

    /**
     * ### Gets pending write bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::pendingBytes() To get the number of pending bytes.
     *
     * @return non-negative-int Pending bytes.
     */
    public function pendingBytes ():int {

        return $this->writer->pendingBytes();

    }

    /**
     * ### Gets unread buffered bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::unreadBytes() To get the number of unread bytes.
     *
     * @return non-negative-int Unread bytes.
     */
    public function unreadBytes ():int {

        return $this->reader->unreadBytes();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::close() To close the underlying writer.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::close() To close the underlying reader.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the underlying stream cannot be closed.
     *
     * @return void
     */
    public function close ():void {

        if ($this->closed) return;

        $this->writer->close();

        $this->reader->close();

        $this->closed = true;

    }

    /**
     * ### Prepares the stream for reading
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Writer::flush() To flush pending writes.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered writer is closed.
     *
     * @return void
     */
    private function prepareRead ():void {

        if ($this->writing) {

            $this->writer->flush();

            $this->writing = false;

        }

        $this->reading = true;

    }

    /**
     * ### Prepares the stream for writing
     *
     * Restores the logical position when unread prefetched bytes remain.
     *
     * Non-seekable streams cannot safely switch from buffered reading to writing while unread data remains.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::unreadBytes() To get the number of unread bytes.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::clear() To clear the buffered reader.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::isSeekable() To check if the underlying stream is seekable.
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Reader::seek() To seek to the current position.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered reader is closed or the stream
     * cannot be seeked.
     *
     * @return void
     */
    private function prepareWrite ():void {

        if ($this->reading) {

            $unread = $this->reader->unreadBytes();

            if ($unread > 0) {

                if (!$this->stream instanceof Seekable || !$this->stream->isSeekable())
                    throw new StreamException(
                        'Cannot switch from reading to writing with unread buffered data on a non-seekable stream.'
                    );

                $this->stream->seek(
                    -$unread,
                    SeekOrigin::CURRENT
                );

            }

            $this->reader->clear();

            $this->reading = false;

        }

        $this->writing = true;

    }

    /**
     * ### Requires an open stream
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\Buffer\Stream::isClosed() To check if the buffered stream is closed.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the buffered stream is closed.
     */
    private function requireOpen ():void {

        if ($this->isClosed())
            throw new StreamException(
                'The buffered stream is closed.'
            );

    }

}