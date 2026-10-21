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
 * ### Provides a Stream Source that lazily re-indexes elements
 *
 * ReindexSource decorates another Source and replaces its existing keys with sequential integer keys while
 * preserving the original values and iteration order.
 *
 * Reindexing is performed lazily as elements are consumed and begins at the configured starting index.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<int, TValue>
 */
final readonly class ReindexSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source whose elements are reindexed.
     * </p>
     * @param int $start <p>
     * The starting index.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Source $source,
        private int $start = 0
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source::iterate() To iterate over the underlying Source elements.
     */
    public function iterate ():iterable {

        $index = $this->start;

        foreach ($this->source->iterate() as $value)
            yield $index++ => $value;

    }

}