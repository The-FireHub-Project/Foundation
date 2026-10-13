<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Factory;

use FireHub\Foundation\DataStructure\Heap;
use FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage;
use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
use Closure;

/**
 * ### Provides factory methods for creating heap data structures
 *
 * Provides convenient factory methods for creating minimum and maximum heaps using the Foundation data structure
 * implementation.
 *
 * The factory encapsulates the storage, comparison, and initialization details required to construct a heap,
 * allowing callers to create heaps without directly depending on the underlying storage implementation or its
 * initialization strategies.
 *
 * Heaps created by this factory use binary heap storage as their underlying priority-ordered storage implementation.
 * @since 1.0.0
 */
readonly class HeapFactory {

    /**
     * ### Creates a minimum heap
     *
     * The smallest value has the highest priority.
     * @since 1.0.0
     *
     * @template TValue
     *
     * @param array<TValue> $values <p>
     * The values to initialize the heap with.
     * </p>
     * @param Closure(TValue, TValue):int $comparator <p>
     * Comparison strategy used to determine precedence between values.
     *
     * A negative result indicates that the first value has precedence, a positive result indicates that
     * the second value has precedence, and zero indicates equivalent precedence.
     * </p>
     *
     * @return Heap<TValue>
     */
    public static function min (array $values, Closure $comparator):Heap {

        return new Heap(
            new BinaryHeapStorage(
                $comparator(...),
                new ArrayInit($values)
            )
        );

    }

    /**
     * ### Creates a maximum heap
     *
     * The largest value has the highest priority.
     * @since 1.0.0
     *
     * @template TValue
     *
     * @param array<TValue> $values <p>
     * The values to initialize the heap with.
     * </p>
     * @param Closure(TValue, TValue):int $comparator <p>
     * Comparison strategy used to determine precedence between values.
     *
     * A negative result indicates that the first value has precedence, a positive result indicates that
     * the second value has precedence, and zero indicates equivalent precedence.
     * </p>
     *
     * @return Heap<TValue>
     */
    public static function max (array $values, Closure $comparator):Heap {

        return new Heap(
            new BinaryHeapStorage(
                static fn (mixed $left, mixed $right):int => $comparator($right, $left),
                new ArrayInit($values)
            )
        );

    }

}