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
 * ### Provides a Stream Source that lazily observes elements
 *
 * TapSource decorates another Source and invokes a callback for each element as it is produced while yielding the
 * original key and value unchanged.
 *
 * The callback is executed lazily during iteration and may be used to perform side effects such as logging,
 * debugging, tracing, or collecting metrics without modifying the Stream elements.
 *
 * Replayability and consumption characteristics remain determined by the underlying Source. When the underlying
 * Source is replayable, the callback is invoked again for every traversal.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue>
 */
final readonly class TapSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source whose elements are observed.
     * </p>
     * @param Closure(TValue, TKey=):void $callback <p>
     * The callback invoked for each produced element.
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

        foreach ($this->source->iterate() as $key => $value) {

            ($this->callback)($value, $key);

            yield $key => $value;

        }

    }

}