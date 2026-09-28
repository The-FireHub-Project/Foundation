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

namespace FireHub\Foundation\Algorithm\Sorting;

use FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm;

/**
 * ### Bubble sort algorithm
 *
 * Implements the bubble sort comparison sorting algorithm.
 *
 * The algorithm repeatedly compares adjacent elements and exchanges them when they are out of order. After each
 * pass, the largest remaining element, according to the supplied comparator, is placed at the end of the unsorted
 * range.
 *
 * Processing stops early when a complete pass performs no exchanges, allowing already ordered input to complete
 * in linear time.
 *
 * The average and worst-case time complexity is O(n²), while the best-case time complexity is O(n) for already
 * ordered input. The algorithm operates in place and is stable when elements comparing as equal are not exchanged.
 *
 * ##### Recommended usage
 *
 * Bubble sort is primarily suitable for educational purposes, algorithm verification, and very small data sets.
 * Its simple behavior can also make it useful as a reference implementation when validating storage adapters and
 * custom comparators.
 *
 * It should generally not be used for performance-sensitive sorting of medium or large data sets because its average
 * and worst-case time complexity is O(n²).
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm<TElement>
 */
final readonly class BubbleSort implements SortAlgorithm {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function sort (int $size, callable $element, callable $set, callable $swap, callable $comparator):void {

        if ($size < 2) return;

        for ($end = $size - 1; $end > 0; $end--) {

            $swapped = false;

            for ($index = 0; $index < $end; $index++) {

                $next = $index + 1;

                if ($comparator($element($index), $element($next)) <= 0)
                    continue;

                $swap($index, $next);

                $swapped = true;

            }

            if (!$swapped) return;

        }

    }

}