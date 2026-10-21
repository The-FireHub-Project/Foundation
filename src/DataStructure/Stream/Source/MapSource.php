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
 * ### Provides a Stream Source that lazily maps values
 *
 * MapSource decorates another Source and applies a mapping operation to each value as it is produced.
 *
 * Values are transformed lazily during iteration without materializing the underlying sequence, while the original
 * source keys are preserved.
 *
 * The mapping operation is not executed until elements are requested by a consumer. Replayability and consumption
 * characteristics therefore remain determined by the underlying Source.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue>
 */
final readonly class MapSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source to map.
     * </p>
     * @param Closure(TValue, TKey=):TValue $callback <p>
     * The mapping function to apply to each value.
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
            yield $key => ($this->callback)($value, $key);

    }

}