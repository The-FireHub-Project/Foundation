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
 * ### Selection sort algorithm
 *
 * Implements the selection sort comparison sorting algorithm.
 *
 * The algorithm divides the element range into ordered and unordered regions. During each pass, it searches the
 * unordered region for the element that should appear next according to the supplied comparator and exchanges that
 * element with the first element of the unordered region.
 *
 * Selection sort performs at most one exchange per pass, making it useful when exchanging elements is significantly
 * more expensive than comparing them.
 *
 * ##### Recommended usage
 *
 * Selection sort is suitable for small data sets and scenarios where minimizing the number of element exchanges is
 * more important than minimizing the number of comparisons.
 *
 * It should generally not be used for performance-sensitive sorting of medium or large data sets because it performs
 * O(n²) comparisons regardless of the initial ordering of the input.
 *
 * The best, average, and worst-case time complexity is O(n²). The algorithm operates in place and requires O(1)
 * additional storage. The algorithm is not stable because exchanging a selected element with the first element of the
 * unordered region can change the relative order of elements considered equal by the comparator.
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm<TElement>
 */
final readonly class SelectionSort implements SortAlgorithm {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function sort (int $size, callable $element, callable $set, callable $swap, callable $comparator):void {

        if ($size < 2) return;

        for ($index = 0; $index < $size - 1; $index++) {

            $selected = $index;
            $selected_value = $element($index);

            for ($candidate = $index + 1; $candidate < $size; $candidate++) {

                $candidate_value = $element($candidate);

                if ($comparator($candidate_value, $selected_value) >= 0)
                    continue;

                $selected = $candidate;
                $selected_value = $candidate_value;

            }

            if ($selected !== $index)
                $swap($index, $selected);

        }

    }

}