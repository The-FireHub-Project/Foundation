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
use FireHub\Runtime;

/**
 * ### Quick sort algorithm
 *
 * Implements the quicksort comparison sorting algorithm.
 *
 * The algorithm operates on elements addressed by sequential positions and uses callbacks to access and exchange
 * elements. This keeps the implementation independent of the underlying representation and allows it to sort both
 * indexed values and key-value entries.
 *
 * A median-of-three strategy is used to select the pivot from the first, middle, and last elements of each range,
 * reducing the likelihood of consistently poor pivot selection for already ordered or partially ordered input.
 *
 * The smaller partition is processed recursively while the larger partition is processed iteratively. This limits
 * recursion depth and reduces stack usage compared to recursively processing both partitions.
 *
 * The average time complexity is O(n log n), while the worst-case time complexity remains O(n²). The algorithm is not
 * stable, meaning that elements considered equal by the comparator are not guaranteed to preserve their original order.
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm<TElement>
 */
final readonly class QuickSort implements SortAlgorithm {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\QuickSort::quickSort() To recursively sort the element range.
     */
    public function sort (int $size, callable $element, callable $swap, callable $comparator):void {

        if ($size < 2) return;

        $this->quickSort(0, $size - 1, $element, $swap, $comparator);

    }

    /**
     * ### Sorts an element range
     *
     * Repeatedly partitions the specified range and recursively processes only the smaller partition.
     *
     * Processing the larger partition iteratively limits recursion depth while preserving quicksort semantics.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\QuickSort::partition() To partition the range.
     *
     * @param non-negative-int $low <p>
     * Inclusive lower boundary of the range.
     * </p>
     * @param non-negative-int $high <p>
     * Inclusive upper boundary of the range.
     * </p>
     * @param callable(non-negative-int):TElement $element <p>
     * Callback used to retrieve an element by its sequential position.
     * </p>
     * @param callable(non-negative-int, non-negative-int):void $swap <p>
     * Callback used to exchange two elements by their sequential positions.
     * </p>
     * @param callable(TElement, TElement):int $comparator <p>
     * Callback used to compare two elements.
     * </p>
     *
     * @return void
     */
    private function quickSort (int $low, int $high, callable $element, callable $swap, callable $comparator):void {

        while ($low < $high) {

            $pivot = $this->partition(
                $low, $high, $element, $swap, $comparator
            );

            $left_size = $pivot - $low;
            $right_size = $high - $pivot;

            if ($left_size < $right_size) {

                if ($low < $pivot - 1)
                    $this->quickSort(
                        $low, $pivot - 1, $element, $swap, $comparator
                    );

                $low = $pivot + 1;

            } else {

                if ($pivot + 1 < $high)
                    $this->quickSort(
                        $pivot + 1, $high, $element, $swap, $comparator
                    );

                $high = $pivot - 1;

            }

        }

    }

    /**
     * ### Partitions an element range
     *
     * Selects a pivot using the median-of-three strategy and partitions the range around that pivot.
     *
     * Elements comparing before or equal to the pivot are placed before it, while elements comparing after the pivot
     * are placed after it. The pivot is then placed at its final position.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\QuickSort::pivot() To select the pivot position.
     *
     * @param non-negative-int $low <p>
     * Inclusive lower boundary of the range.
     * </p>
     * @param non-negative-int $high <p>
     * Inclusive upper boundary of the range.
     * </p>
     * @param callable(non-negative-int):TElement $element <p>
     * Callback used to retrieve an element by its sequential position.
     * </p>
     * @param callable(non-negative-int, non-negative-int):void $swap <p>
     * Callback used to exchange two elements by their sequential positions.
     * </p>
     * @param callable(TElement, TElement):int $comparator <p>
     * Callback used to compare two elements.
     * </p>
     *
     * @return non-negative-int Final sequential position of the pivot element.
     */
    private function partition (int $low, int $high, callable $element, callable $swap, callable $comparator):int {

        $pivot_index = $this->pivot($low, $high, $element, $comparator);

        if ($pivot_index !== $high) $swap($pivot_index, $high);

        $pivot = $element($high);
        $position = $low;

        for ($index = $low; $index < $high; $index++) {

            if ($comparator($element($index), $pivot) > 0)
                continue;

            if ($position !== $index)
                $swap($position, $index);

            $position++;

        }

        if ($position !== $high) $swap($position, $high);

        return $position;

    }

    /**
     * ### Selects a pivot
     *
     * Selects the median element among the first, middle, and last elements of the specified range according to the
     * supplied comparator.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::divideInt() To calculate the median position.
     *
     * @param non-negative-int $low <p>
     * Inclusive lower boundary of the range.
     * </p>
     * @param non-negative-int $high <p>
     * Inclusive upper boundary of the range.
     * </p>
     * @param callable(non-negative-int):TElement $element <p>
     * Callback used to retrieve an element by its sequential position.
     * </p>
     * @param callable(TElement, TElement):int $comparator <p>
     * Callback used to compare two elements.
     * </p>
     *
     * @return non-negative-int Position of the selected pivot element.
     */
    private function pivot (int $low, int $high, callable $element, callable $comparator):int {

        /** @var non-negative-int $middle */
        $middle = $low + Runtime\Math::divideInt($high - $low, 2);

        $low_value = $element($low);
        $middle_value = $element($middle);
        $high_value = $element($high);

        if ($comparator($low_value, $middle_value) < 0) {

            if ($comparator($middle_value, $high_value) < 0)
                return $middle;

            return $comparator($low_value, $high_value) < 0
                ? $high : $low;

        }

        if ($comparator($low_value, $high_value) < 0)
            return $low;

        return $comparator($middle_value, $high_value) < 0
            ? $high : $middle;

    }

}