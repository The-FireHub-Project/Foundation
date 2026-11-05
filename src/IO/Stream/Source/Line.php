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

namespace FireHub\Foundation\IO\Stream\Source;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\DataStructure\Stream\Source;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Line Stream Source
 *
 * Adapts a readable I/O stream into a lazy sequence of text lines.
 *
 * Lines are separated by LF characters. CRLF line endings are normalized by removing the trailing CR character.
 *
 * Line delimiters are excluded from returned values.
 *
 * The final line is returned even when it does not end with a newline.
 *
 * Iteration starts at the current stream position and does not automatically rewind or close the underlying stream.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<int, string>
 */
final readonly class Line implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\IO\Stream\Readable $stream <p>
     * Source stream.
     * </p>
     * @param positive-int $chunk_size [optional] <p>
     * Maximum bytes read per operation.
     *
     * Defaults to 8192.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the chunk size is invalid.
     *
     * @return void
     */
    public function __construct (
        private Readable $stream,
        private int $chunk_size = 8192
    ) {

        if ($chunk_size <= 0)
            throw new StreamException(
                'Chunk size must be greater than zero.'
            );

    }

    /**
     * ### Iterates over lines
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::read() To read from the stream.
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::eof() To check if the stream is at EOF.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the first occurrence of a substring.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To extract a substring.
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if a string ends with a specific substring.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream returns no data before EOF.
     *
     * @return iterable<int, string> Lines without line delimiters.
     */
    public function iterate ():iterable {

        $buffer = ''; $index = 0;
        while (!$this->stream->eof()) {

            $chunk = $this->stream->read($this->chunk_size);

            if ($chunk === '') {

                if ($this->stream->eof()) // @phpstan-ignore if.alwaysFalse
                    break;

                throw new StreamException(
                    'The stream returned no data before reaching EOF.'
                );

            }

            $buffer .= $chunk;

            while (($position = Runtime\Str\SB\Search::firstPosition("\n", $buffer)) !== false) {

                $line = Runtime\Str\SB\Access::part($buffer, 0, $position);

                $buffer = Runtime\Str\SB\Access::part($buffer, $position + 1);

                if (Runtime\Str\SB\Search::endsWith("\r", $line))
                    $line = Runtime\Str\SB\Access::part($line, 0, -1);

                yield $index++ => $line;

            }

        }

        if ($buffer !== '')
            yield $index => $buffer;

    }

}