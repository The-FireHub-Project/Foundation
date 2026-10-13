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
use Closure;

/**
 * ### Provides a Stream Source that lazily flat maps values
 *
 * FlatMapSource decorates another Source and applies a mapping operation that produces the iterable for each source
 * element.
 *
 * Produced iterables are flattened into a single sequence as they are consumed without materializing either the
 * underlying source or the mapped iterables.
 *
 * The mapping operation is not executed until elements are requested by a consumer. Replayability and consumption
 * characteristics therefore remain determined by the underlying Source and the iterables produced by the callback.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 * @template TNewKey
 * @template TNewValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TNewKey, TNewValue>
 */
final readonly class FlatMapSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source to flat map.
     * </p>
     * @param Closure(TValue, TKey=):iterable<TNewKey, TNewValue> $callback <p>
     * The mapping function used to produce values for each source element.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Source $source,
        private Closure $callback
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source::iterate() To iterate over the underlying Source elements.
     */
    public function iterate ():iterable {

        foreach ($this->source->iterate() as $key => $value)
            yield from ($this->callback)($value, $key);

    }

}