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

namespace FireHub\Foundation\Algorithm\Sorting\Distribution;

use FireHub\Core\Boundary\Algorithm\Sorting\DistributionSortAlgorithm;
use FireHub\Runtime;

/**
 * ### Counting sort algorithm
 *
 * Implements the counting sort non-comparison sorting algorithm.
 *
 * The algorithm extracts an integer key from each element, determines the key range, and counts the number of
 * occurrences of every key within that range. Cumulative counts are then used to place elements into their final
 * ordered positions.
 *
 * Elements are processed from the end of the input while constructing the output, preserving the relative order of
 * elements having equal keys and making the implementation stable.
 *
 * ##### Recommended usage
 *
 * Counting sort is suitable for integer-keyed data sets where the range between the minimum and maximum keys is
 * reasonably small relative to the number of elements.
 *
 * It can significantly outperform comparison-based sorting algorithms when sorting large collections containing
 * values from a small integer range.
 *
 * It should generally not be used when the key range is very large or sparse because memory consumption is
 * proportional to the key range rather than only to the number of elements.
 *
 * The time complexity is O(n + k), where n is the number of elements and k is the integer key range. The algorithm
 * requires O(n + k) additional storage and is stable.
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\DistributionSortAlgorithm<TElement>
 */
final readonly class CountingSort implements DistributionSortAlgorithm {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Structure::fill() To fill an array with a value.
     */
    public function sort (int $size, callable $element, callable $set, callable $key):void {

        if ($size < 2) return;

        $keys = [];

        $first = $key($element(0));

        $minimum = $first;
        $maximum = $first;

        $keys[0] = $first;

        for ($index = 1; $index < $size; $index++) {

            $current = $key($element($index));

            $keys[$index] = $current;

            if ($current < $minimum)
                $minimum = $current;

            if ($current > $maximum)
                $maximum = $current;

        }

        /** @var positive-int $range */
        $range = ($maximum - $minimum) + 1;

        $counts = Runtime\Arr\Structure::fill(0, 0, $range);

        for ($index = 0; $index < $size; $index++)
            $counts[$keys[$index] - $minimum]++; // @phpstan-ignore-line

        for ($index = 1; $index < $range; $index++)
            $counts[$index] += $counts[$index - 1]; // @phpstan-ignore-line

        $output = [];

        for ($index = $size - 1; $index >= 0; $index--) {

            $offset = $keys[$index] - $minimum; // @phpstan-ignore offsetAccess.notFound

            $position = --$counts[$offset]; // @phpstan-ignore offsetAccess.notFound

            $output[$position] = $element($index);

        }

        for ($index = 0; $index < $size; $index++)
            $set($index, $output[$index]); // @phpstan-ignore offsetAccess.notFound

    }

}