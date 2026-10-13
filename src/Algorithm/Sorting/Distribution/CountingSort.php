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

namespace FireHub\Foundation\Algorithm\Sorting\Distribution;

use FireHub\Core\Boundary\Algorithm\Sorting\DistributionSortAlgorithm;
use FireHub\Core\Foundation\Constant\Numeric\IntegerLimits;
use FireHub\Foundation\Algorithm\Sorting\Exception\CountingSortRangeException;
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
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\DistributionSortAlgorithm<TElement, int>
 */
final readonly class CountingSort implements DistributionSortAlgorithm {

    /**
     * ### Minimum supported key range
     *
     * Defines the minimum key range that may be processed regardless of the number of elements.
     * @since 1.0.0
     */
    private const int MINIMUM_RANGE_LIMIT = 256;

    /**
     * ### Default key range factor
     *
     * Defines the default maximum key range relative to the number of elements.
     * @since 1.0.0
     */
    private const int DEFAULT_RANGE_FACTOR = 4;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param positive-int $rangeFactor <p>
     * Maximum supported key range relative to the number of elements.
     * </p>
     */
    public function __construct (
        private int $rangeFactor = self::DEFAULT_RANGE_FACTOR
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\CountingSort::rangeLimit() To determine the maximum
     * supported key range.
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\CountingSort::exceedsRange() To determine whether the
     * key range exceeds the supported range.
     * @uses \FireHub\Runtime\Arr\Structure::fill() To fill an array with a value.
     *
     * @throws \FireHub\Foundation\Algorithm\Sorting\Exception\CountingSortRangeException If the integer key range
     * exceeds the supported range.
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

        $limit = $this->rangeLimit($size);

        if ($this->exceedsRange($minimum, $maximum, $limit))
            throw new CountingSortRangeException(
                'Counting sort key range exceeds the supported limit.',
                [
                    'minimum' => $minimum,
                    'maximum' => $maximum,
                    'limit' => $limit
                ]
            );

        /**
         * Safe because exceedsRange() guarantees that the difference between the maximum and minimum keys is smaller
         * than the supported range limit.
         *
         * @var positive-int $range
         */
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

    /**
     * ### Determines the supported key range limit
     *
     * Calculates the maximum key range that may be processed according to the number of elements and configured range
     * factor while preventing integer overflow.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\CountingSort::MINIMUM_RANGE_LIMIT To define the
     * minimum supported key range.
     * @uses \FireHub\Runtime\Math::divideInt() To calculate the maximum key range.
     * @uses \FireHub\Runtime\Math::max() To ensure the range limit is not less than the minimum supported key range.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::MAX To define the maximum integer key.
     *
     * @param positive-int $size <p>
     * Number of elements.
     * </p>
     *
     * @return positive-int Maximum supported key range.
     */
    private function rangeLimit (int $size):int {

        if ($size > Runtime\Math::divideInt(IntegerLimits::MAX, $this->rangeFactor))
            return IntegerLimits::MAX;

        /** @var positive-int */
        return Runtime\Math::max(
            self::MINIMUM_RANGE_LIMIT,
            $size * $this->rangeFactor
        );

    }

    /**
     * ### Determines whether a key range exceeds the supported limit
     *
     * Compares the minimum and maximum integer keys without directly subtracting values that could exceed the native
     * integer range.
     * @since 1.0.0
     *
     * @param int $minimum <p>
     * Minimum integer key.
     * </p>
     * @param int $maximum <p>
     * Maximum integer key.
     * </p>
     * @param positive-int $limit <p>
     * Maximum supported key range.
     * </p>
     *
     * @return bool True if the key range exceeds the supported limit, otherwise false.
     */
    private function exceedsRange (int $minimum, int $maximum, int $limit):bool {

        /**
         * Values on the same side of zero can be safely subtracted because their difference cannot exceed the native
         * positive integer range.
         */
        if ($minimum >= 0 || $maximum < 0)
            return $maximum - $minimum >= $limit;

        /**
         * The range crosses zero. Distances are calculated independently to avoid overflowing maximum - minimum.
         *
         * Adding one before negating also makes PHP_INT_MIN safe:
         *
         *     -(PHP_INT_MIN + 1)
         *
         * Can be represented as an integer while -PHP_INT_MIN cannot.
         */
        $negative = -($minimum + 1);

        if ($negative >= $limit - 1)
            return true;

        return $maximum >= ($limit - 1) - $negative;

    }

}