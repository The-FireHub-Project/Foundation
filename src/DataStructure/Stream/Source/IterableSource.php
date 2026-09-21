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

namespace FireHub\Foundation\DataStructure\Stream\Source;

use FireHub\Foundation\DataStructure\Stream\Source;

/**
 * ### Provides a Stream Source backed by an existing iterable
 *
 * IterableSource adapts the iterable into a Stream Source while preserving its keys and values.
 *
 * The supplied iterable is consumed directly and is not copied or materialized by the Source. This allows arrays,
 * iterators, generators, and other iterable implementations to participate in Stream processing without requiring
 * an intermediate storage representation.
 *
 * Replayability depends entirely on the supplied iterable. Arrays and other replayable iterables may generally be
 * consumed repeatedly, while generators and other single-consumption iterables may only support one traversal.
 *
 * No assumptions are made about the size or finiteness of the supplied iterable.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue>
 */
final readonly class IterableSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param iterable<TKey, TValue> $iterable <p>
     * The iterable that provides the source elements.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private iterable $iterable
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function iterate ():iterable {

        yield from $this->iterable;

    }

}