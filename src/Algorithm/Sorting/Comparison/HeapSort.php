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
 * ### Heap sort algorithm
 *
 * Implements the heap sort comparison sorting algorithm.
 *
 * The algorithm first arranges the element range into a binary heap according to the supplied comparator. The element
 * that should appear last, according to comparator, is maintained at the root of the heap.
 *
 * After the heap has been constructed, the root element is repeatedly exchanged with the final element of the
 * remaining heap. The heap property is then restored for the reduced range until all elements are ordered.
 *
 * Heap construction is performed bottom-up in O(n) time, while each subsequent extraction requires O(log n) time.
 *
 * ##### Recommended usage
 *
 * Heap sort is suitable for medium and large data sets when guaranteed O(n log n) worst-case performance is required
 * without allocating additional proportional storage.
 *
 * It is particularly useful when predictable worst-case behavior and constant additional storage are more important
 * than stable ordering.
 *
 * When stable ordering is required, the merge sort should be preferred. Quick sort may provide better average practical
 * performance when its O(n²) worst case is acceptable. For small or nearly ordered data sets, the insertion sort may be
 * more appropriate.
 *
 * The best, average, and worst-case time complexity is O(n log n). The algorithm operates in place, requires O(1)
 * additional storage, and is not stable.
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm<TElement>
 */
final readonly class HeapSort implements SortAlgorithm {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\HeapSort::siftDown() To restore the heap property.
     * @uses \FireHub\Runtime\Math::divideInt() To calculate the root position.
     */
    public function sort (int $size, callable $element, callable $set, callable $swap, callable $comparator):void {

        if ($size < 2) return;

        // Build the heap bottom-up.
        for ($root = Runtime\Math::divideInt($size, 2) - 1; $root >= 0; $root--)
            $this->siftDown($root, $size, $element, $swap, $comparator);

        /**
         * Repeatedly move the root to its final position and restore
         * the heap property for the remaining range.
         */
        for ($end = $size - 1; $end > 0; $end--) {

            $swap(0, $end);

            $this->siftDown(0, $end, $element, $swap, $comparator);

        }

    }

    /**
     * ### Restores the heap property
     *
     * Moves the element at the specified root position down the heap until the heap property has been restored within
     * the specified range.
     * @since 1.0.0
     *
     * @param non-negative-int $root <p>
     * Root position.
     * </p>
     * @param positive-int $size <p>
     * Number of elements contained in the heap.
     * </p>
     * @param callable(non-negative-int):TElement $element <p>
     * Element accessor.
     * </p>
     * @param callable(non-negative-int, non-negative-int):void $swap <p>
     * Element exchanger.
     * </p>
     * @param callable(TElement, TElement):int $comparator <p>
     * Element comparator.
     * </p>
     *
     * @return void
     */
    private function siftDown (int $root, int $size, callable $element, callable $swap, callable $comparator):void {

        while (true) {

            $selected = $root;
            $left = ($root * 2) + 1;

            if ($left >= $size) return;

            if ($comparator($element($left), $element($selected)) > 0)
                $selected = $left;

            $right = $left + 1;

            if (
                $right < $size
                && $comparator($element($right), $element($selected)) > 0
            ) $selected = $right;

            if ($selected === $root) return;

            $swap($root, $selected);

            $root = $selected;

        }

    }

}