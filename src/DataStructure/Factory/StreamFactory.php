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

namespace FireHub\Foundation\DataStructure\Factory;

use FireHub\Foundation\DataStructure\Stream;
use FireHub\Foundation\DataStructure\Stream\Source\ {
    FactorySource, IterableSource
};
use Closure;

/**
 * ### Provides factory methods for creating stream data structures
 *
 * Provides convenient factory methods for creating streams using the Foundation data structure implementation.
 *
 * The factory encapsulates the source details required to construct a stream, allowing callers to create streams
 * without directly depending on the underlying source implementation.
 *
 * Streams created by this factory preserve lazy evaluation whenever supported by the underlying source, allowing
 * values to be produced on demand without materializing the complete sequence in memory.
 * @since 1.0.0
 */
readonly class StreamFactory {

    /**
     * ### Creates a stream from an iterable
     *
     * Creates a new stream that produces values from the provided iterable.
     *
     * Values are consumed from the iterable on demand as the stream is traversed, avoiding eager materialization of
     * the complete sequence.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\IterableSource To provide values from an iterable.
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $values <p>
     * The iterable containing values to produce.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Stream<TKey, TValue> A stream producing the provided values.
     */
    public function iterable (iterable $values):Stream {

        return new Stream(
            new IterableSource($values)
        );

    }

    /**
     * ### Creates a stream from a source factory
     *
     * Creates a new stream using iterable returned by the provided callback.
     *
     * The callback is invoked when stream iteration begins, allowing the underlying sequence to be created lazily
     * for each traversal of the stream.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\FactorySource To lazily create the stream source.
     *
     * @template TKey
     * @template TValue
     *
     * @param Closure():iterable<TKey, TValue> $factory <p>
     * The callback that returns an iterable used to produce stream values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Stream<TKey, TValue> A stream producing values returned by the factory.
     */
    public function factory (Closure $factory):Stream {

        return new Stream(
            new FactorySource($factory)
        );

    }

}