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
use Iterator;

/**
 * ### Provides a Stream Source that lazily zips elements
 *
 * ZipSource decorates another Source and combines its values with values from another iterable into pairs.
 *
 * Elements are consumed lazily from both sequences in parallel. Iteration stops as soon as either sequence is
 * exhausted.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 * @template TOtherValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TKey, array{TValue, TOtherValue}>
 */
final readonly class ZipSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source whose elements are zipped.
     * </p>
     * @param iterable<mixed, TOtherValue> $values <p>
     * The values to zip with the Source elements.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Source $source,
        private iterable $values
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source::iterate() To iterate over the underlying Source elements.
     */
    public function iterate ():iterable {

        $iterator = $this->iterator($this->values);

        foreach ($this->source->iterate() as $key => $value) {

            if (!$iterator->valid())
                break;

            yield $key => [
                $value,
                $iterator->current()
            ];

            $iterator->next();

        }

    }

    /**
     * ### Creates an iterator
     *
     * Converts the specified iterable into an Iterator that can be consumed alongside the underlying Source.
     * @since 1.0.0
     *
     * @template T
     *
     * @param iterable<mixed, T> $values <p>
     * The iterable to convert.
     * </p>
     *
     * @return \Iterator<mixed, T> The iterator.
     */
    private function iterator (iterable $values):Iterator {

        yield from $values;

    }

}