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

namespace FireHub\Foundation\Algorithm\Sorting\Comparison;

use FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm;
use FireHub\Runtime;

/**
 * ### Merge sort algorithm
 *
 * Implements the merge sort comparison sorting algorithm.
 *
 * The algorithm recursively divides the element range into smaller contiguous ranges until each range contains a
 * single element. The resulting ranges are then merged in order according to the supplied comparator.
 *
 * A temporary buffer is allocated once for the complete sorting operation and reused while merging ranges, avoiding
 * repeated allocation of intermediate arrays during recursive processing.
 *
 * Merge sort preserves the relative order of elements considered equal by the comparator, making the implementation
 * stable.
 *
 * ##### Recommended usage
 *
 * Merge sort is suitable for medium and large data sets when predictable O(n log n) performance and stable ordering
 * are required.
 *
 * It is particularly useful when preserving the relative order of equal elements is more important than minimizing
 * additional memory usage.
 *
 * When additional proportional storage should be avoided, an in-place sorting algorithm such as quick sort or heap
 * sort may be more appropriate. For small or nearly ordered data sets, insertion sort may provide better performance.
 *
 * The best, average, and worst-case time complexity is O(n log n). The algorithm requires O(n) additional storage
 * for the temporary merge buffer and O(log n) recursion stack space.
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm<TElement>
 */
final readonly class MergeSort implements SortAlgorithm {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\MergeSort::mergeSort() To recursively sort the element range.
     */
    public function sort (int $size, callable $element, callable $set, callable $swap, callable $comparator):void {

        if ($size < 2) return;

        $buffer = [];

        $this->mergeSort(0, $size - 1, $buffer, $element, $set, $comparator);

    }

    /**
     * ### Sorts an element range
     *
     * Recursively divides the specified range into two smaller ranges and merges them after both ranges have been
     * ordered.
     * @since 1.0.0
     *
     * @param non-negative-int $low <p>
     * Inclusive lower boundary of the range.
     * </p>
     * @param non-negative-int $high <p>
     * Inclusive upper boundary of the range.
     * </p>
     * @param array<non-negative-int, TElement> $buffer <p>
     * Temporary merge buffer.
     * </p>
     * @param callable(non-negative-int):TElement $element <p>
     * Element accessor.
     * </p>
     * @param callable(non-negative-int, TElement):void $set <p>
     * Element writer.
     * </p>
     * @param callable(TElement, TElement):int $comparator <p>
     * Element comparator.
     * </p>
     *
     * @return void
     */
    private function mergeSort (int $low, int $high, array &$buffer, callable $element, callable $set, callable $comparator):void {

        if ($low >= $high) return;

        /** @var non-negative-int $middle */
        $middle = $low + Runtime\Math::divideInt($high - $low, 2);

        $this->mergeSort($low, $middle, $buffer, $element, $set, $comparator);

        $this->mergeSort($middle + 1, $high, $buffer, $element, $set, $comparator);

        // Both ranges are already in their final relative order.
        if ($comparator($element($middle), $element($middle + 1)) <= 0)
            return;

        $this->merge($low, $middle, $high, $buffer, $element, $set, $comparator);

    }

    /**
     * ### Merges two ordered ranges
     *
     * Copies the specified range into the temporary buffer and merges the two ordered subranges back into the
     * underlying storage.
     * @since 1.0.0
     *
     * @param non-negative-int $low <p>
     * Inclusive lower boundary of the range.
     * </p>
     * @param non-negative-int $middle <p>
     * Inclusive upper boundary of the first range.
     * </p>
     * @param non-negative-int $high <p>
     * Inclusive upper boundary of the range.
     * </p>
     * @param array<non-negative-int, TElement> $buffer <p>
     * Temporary merge buffer.
     * </p>
     * @param callable(non-negative-int):TElement $element <p>
     * Element accessor.
     * </p>
     * @param callable(non-negative-int, TElement):void $set <p>
     * Element writer.
     * </p>
     * @param callable(TElement, TElement):int $comparator <p>
     * Element comparator.
     * </p>
     *
     * @return void
     */
    private function merge (int $low, int $middle, int $high, array &$buffer, callable $element, callable $set, callable $comparator):void {

        for ($index = $low; $index <= $high; $index++)
            $buffer[$index] = $element($index);

        $left = $low; $right = $middle + 1;
        for ($index = $low; $index <= $high; $index++) {

            if ($left > $middle) {

                $set($index, $buffer[$right++]); // @phpstan-ignore offsetAccess.notFound

                continue;

            }

            if ($right > $high) {

                $set($index, $buffer[$left++]); // @phpstan-ignore offsetAccess.notFound

                continue;

            }

            if ($comparator($buffer[$left], $buffer[$right]) <= 0) { // @phpstan-ignore-line

                $set($index, $buffer[$left++]); // @phpstan-ignore offsetAccess.notFound

            } else {

                $set($index, $buffer[$right++]); // @phpstan-ignore offsetAccess.notFound

            }

        }

    }

}