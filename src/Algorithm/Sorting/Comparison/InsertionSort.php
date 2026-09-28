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

/**
 * ### Insertion sort algorithm
 *
 * Implements the insertion sort comparison sorting algorithm.
 *
 * The algorithm processes elements sequentially and inserts each element into its correct position within the
 * previously sorted range. Elements that compare after the value being inserted are shifted one position toward
 * the end of the range until the correct insertion position is found.
 *
 * Insertion sort performs particularly well on small or nearly ordered data sets and requires no additional
 * proportional storage. It is also suitable as a finishing algorithm for hybrid sorting strategies where larger
 * ranges are first partitioned by another algorithm.
 *
 * The average and worst-case time complexity is O(n²), while the best-case time complexity is O(n) for already
 * ordered input. The algorithm operates in place and is stable because elements comparing as equal are not moved
 * past one another.
 *
 * ##### Recommended usage
 *
 * Insertion sort is suitable for small data sets and data that is already ordered or nearly ordered. It is also useful
 * as a finishing algorithm for small partitions produced by hybrid sorting algorithms.
 *
 * It should generally not be used for large randomly ordered data sets because its average time complexity is O(n²).
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm<TElement>
 */
final readonly class InsertionSort implements SortAlgorithm {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function sort (int $size, callable $element, callable $set, callable $swap, callable $comparator):void {

        if ($size < 2) return;

        for ($index = 1; $index < $size; $index++) {

            $value = $element($index);
            $position = $index;

            while ($position > 0) {

                $previous = $element($position - 1);

                if ($comparator($previous, $value) <= 0)
                    break;

                $set($position, $previous);

                $position--;

            }

            if ($position !== $index)
                $set($position, $value);

        }

    }

}