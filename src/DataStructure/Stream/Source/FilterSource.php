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
 * ### Provides a Stream Source that lazily filters elements
 *
 * FilterSource decorates another Source and applies a filtering predicate to each element as it is produced.
 *
 * Elements that satisfy the predicate are yielded lazily during iteration without materializing the underlying
 * sequence, while the original source keys are preserved.
 *
 * The filtering predicate is not executed until elements are requested by a consumer. Replayability and consumption
 * characteristics therefore remain determined by the underlying Source.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue>
 */
final readonly class FilterSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source whose elements are filtered.
     * </p>
     * @param Closure(TValue, TKey=):bool $predicate <p>
     * The predicate used to determine whether an element should be included.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Source $source,
        private Closure $predicate
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
            if (($this->predicate)($value, $key))
                yield $key => $value;

    }

}