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
 * ### Provides a concatenated stream source
 *
 * Produces values from the first source followed by values from the second source while preserving their iteration
 * order.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue>
 */
final readonly class ConcatSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $first <p>
     * The values to concatenate.
     * </p>
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $second <p>
     * The values to concatenate.
     * </p>
     */
    public function __construct (
        private Source $first,
        private Source $second
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source::iterate() To iterate over the source.
     */
    public function iterate ():iterable {

        yield from $this->first->iterate();
        yield from $this->second->iterate();

    }

}