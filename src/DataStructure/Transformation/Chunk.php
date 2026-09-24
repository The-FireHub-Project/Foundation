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

namespace FireHub\Foundation\DataStructure\Transformation;

use FireHub\Core\Boundary\Capability\Measurement\Metrics;
use FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable;
use FireHub\Foundation\DataStructure\Stream;
use FireHub\Foundation\DataStructure\Exception\ChunkSizeException;
use FireHub\Runtime;

/**
 * ### Chunk transformation
 *
 * Provides higher-level operations for partitioning a data structure into consecutive chunks.
 *
 * Chunk delegates the primitive chunking operation to the underlying Chunkable data structure while providing
 * convenient strategies for determining chunk boundaries, such as fixed-size and conditional partitioning.
 *
 * The source data structure remains responsible for constructing individual chunks, allowing each generated chunk
 * to preserve the concrete type and semantics of its source.
 *
 * Chunking is performed lazily by the underlying data structure, with generated chunks exposed through a Stream as
 * they are consumed.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 * @template TSource of (
 *     \FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable<TKey, TValue>
 *     &\FireHub\Core\Boundary\Capability\Measurement\Metrics
 * )
 */
final readonly class Chunk {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TSource $source <p>
     * The chunkable data structure used as the source for chunk transformations.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Chunkable&Metrics $source
    ) {}

    /**
     * ### Chunks by size
     *
     * Partitions the source into consecutive chunks containing at most the specified number of elements.
     *
     * The final chunk may contain fewer elements when the number of source elements is not evenly divisible by the
     * requested size.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable::chunkBy() To partition the source.
     *
     * @param positive-int $size <p>
     * Maximum number of elements contained in each chunk.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\ChunkSizeException If size is less than one.
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, TSource> The generated chunks.
     */
    public function bySize (int $size):Stream {

        if ($size < 1)
            throw new ChunkSizeException(
                'The chunk size must be greater than zero.'
            );

        $position = 1;

        return $this->source->chunkBy(
            static function () use (&$position, $size):bool {

                return $position++ % $size === 0;

            }
        );

    }

    /**
     * ### Chunks by count
     *
     * Partitions the source into the specified number of consecutive chunks while distributing elements as evenly as
     * possible between them.
     *
     * When the requested count cannot evenly divide the number of source elements, the additional elements are
     * distributed among the first chunks. The difference in size between generated chunks is therefore at most one
     * element.
     *
     * When the requested count exceeds the number of source elements, each element is placed into its own chunk and no
     * empty chunks are generated.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable::chunkBy() To partition the source.
     * @uses \FireHub\Core\Boundary\Capability\Measurement\Metrics::size() To determine the number of source elements.
     * @uses \FireHub\Runtime\Math::divideInt() To calculate the chunk size.
     * @uses \FireHub\Runtime\Math::min() To ensure the requested count is not greater than the number of source
     * elements.
     *
     * @param positive-int $count <p>
     * Requested number of chunks.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\ChunkSizeException If count is less than one.
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, TSource> The generated chunks.
     */
    public function byCount (int $count):Stream {

        if ($count < 1)
            throw new ChunkSizeException(
                'The chunk count must be greater than zero.'
            );

        $source = $this->source;

        $size = $source->size();

        if ($size === 0)
            return $this->source->chunkBy(
                static fn ():bool => false
            );

        $count = Runtime\Math::min($count, $size);

        $base_size = Runtime\Math::divideInt($size, $count);
        $remainder = $size % $count;

        $chunk = 0; $position = 1;
        $boundary = $base_size + ($chunk < $remainder ? 1 : 0);
        return $this->source->chunkBy(static function () use (
            &$chunk, &$position, &$boundary, $base_size, $remainder
        ):bool {

                $position++;

                if ($position <= $boundary) return false;

                $chunk++;

                $boundary += $base_size + ($chunk < $remainder ? 1 : 0);

                return true;

            }
        );

    }

    /**
     * ### Chunks before condition
     *
     * Starts a new chunk before each element for which the callback evaluates to true.
     *
     * The matching element becomes the first element of the newly created chunk. The first source element always
     * belongs to the first chunk regardless of the callback result.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable::chunkBy() To partition the source.
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines whether a new chunk starts before the current element.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, TSource> The generated chunks.
     */
    public function before (callable $callback):Stream {

        return $this->source->chunkBy($callback);

    }

    /**
     * ### Chunks after condition
     *
     * Starts a new chunk after each element for which the callback evaluates to true.
     *
     * The matching element remains the final element of the current chunk. When another element follows, that element
     * becomes the first element of the next chunk.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable::chunkBy() To partition the source.
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines whether the current chunk ends after the current element.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, TSource> The generated chunks.
     */
    public function after (callable $callback):Stream {

        $boundary = false;

        return $this->source->chunkBy(
            static function ($value, $key = null) use ($callback, &$boundary):bool {

                $start = $boundary;
                $boundary = $callback($value, $key);

                return $start;

            }
        );

    }

}