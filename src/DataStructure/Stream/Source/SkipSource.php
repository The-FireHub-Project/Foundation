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
 * ### Skip stream source
 *
 * Lazily skips consecutive values from the beginning of an underlying stream source while a supplied predicate
 * evaluates to true.
 *
 * Once the predicate first evaluates to false, that value and every subsequent value are yielded without further
 * predicate evaluation.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue>
 */
final readonly class SkipSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source whose elements are taken.
     * </p>
     * @param Closure(TValue, TKey=):bool $callback <p>
     * The predicate used to determine whether to yield a value.
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

        $skipping = true;

        foreach ($this->source->iterate() as $key => $value) {

            if ($skipping) {

                if (($this->callback)($value, $key))
                    continue;

                $skipping = false;

            }

            yield $key => $value;

        }

    }

}