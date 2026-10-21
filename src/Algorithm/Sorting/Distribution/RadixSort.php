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
use FireHub\Runtime;

/**
 * ### Radix sort algorithm
 *
 * Implements the least-significant-digit radix sort non-comparison sorting algorithm for integer keys.
 *
 * Integer keys are processed one byte at a time, beginning with the least significant byte. Each pass performs a
 * stable counting distribution over 256 possible byte values.
 *
 * Signed integer ordering is preserved by transforming the most significant byte so that negative keys are ordered
 * before non-negative keys while retaining the natural ordering within both groups.
 *
 * ##### Recommended usage
 *
 * Radix sort is suitable for large data sets whose values can be represented by integer keys, particularly when the
 * key range is too large or sparse for counting sort.
 *
 * Unlike counting sort, memory consumption does not depend on the distance between the minimum and maximum keys.
 * This makes radix sort suitable for identifiers, timestamps, counters, and other widely distributed integer values.
 *
 * The time complexity is O(p * (n + b)), where n is the number of elements, p is the number of processed bytes, and
 * b is the radix size of 256. For native integers, p is bounded by the number of bytes in the integer representation.
 *
 * The algorithm requires O(n + b) additional storage and is stable.
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\DistributionSortAlgorithm<TElement, int>
 */
final readonly class RadixSort implements DistributionSortAlgorithm {

    /**
     * ### Number of values representable by one byte
     * @since 1.0.0
     */
    private const int RADIX = 256;

    /**
     * ### Sign bit mask
     *
     * Used on the most significant byte to transform signed integer ordering into unsigned byte ordering.
     * @since 1.0.0
     */
    private const int SIGN_MASK = 0x80;

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\RadixSort::extract() To extract values and their integer
     * keys.
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\RadixSort::distribute() To distribute values according
     * to the current byte.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::SIZE To determine the number of bytes in an
     * integer.
     *
     * @return void
     */
    public function sort (int $size, callable $element, callable $set, callable $key):void {

        if ($size < 2) return;

        [$values, $keys] = $this->extract($size, $element, $key);

        for ($byte = 0; $byte < IntegerLimits::SIZE; $byte++)
            [$values, $keys] = $this->distribute($size, $byte, $values, $keys);

        for ($index = 0; $index < $size; $index++)
            $set($index, $values[$index]); // @phpstan-ignore offsetAccess.notFound

    }

    /**
     * ### Extracts values and keys
     *
     * Reads all elements and extracts their integer sorting keys.
     * @since 1.0.0
     *
     * @param positive-int $size <p>
     * Number of elements.
     * </p>
     * @param callable(non-negative-int):TElement $element <p>
     * Element accessor.
     * </p>
     * @param callable(TElement):int $key <p>
     * Integer key extractor.
     * </p>
     *
     * @return array{
     *     array<non-negative-int, TElement>,
     *     array<non-negative-int, int>
     * } Extracted values and keys.
     */
    private function extract (int $size, callable $element, callable $key):array {

        $values = []; $keys = [];
        for ($index = 0; $index < $size; $index++) {

            $value = $element($index);

            $values[$index] = $value;
            $keys[$index] = $key($value);

        }

        return [$values, $keys];

    }

    /**
     * ### Distributes values by byte
     *
     * Performs a stable counting distribution using the specified byte of each integer key.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\RadixSort::digit() To extract the byte value.
     * @uses \FireHub\Runtime\Arr\Structure::fill() To initialize the counting array.
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\RadixSort::RADIX To determine the number of possible
     * byte values.
     *
     * @param positive-int $size <p>
     * Number of elements.
     * </p>
     * @param non-negative-int $byte <p>
     * Byte position.
     * </p>
     * @param array<non-negative-int, TElement> $values <p>
     * Values being sorted.
     * </p>
     * @param array<non-negative-int, int> $keys <p>
     * Integer sorting keys.
     * </p>
     *
     * @return array{
     *     array<non-negative-int, TElement>,
     *     array<non-negative-int, int>
     * } Sorted values and keys.
     */
    private function distribute (int $size, int $byte, array $values, array $keys):array {

        $counts = Runtime\Arr\Structure::fill(0, 0, self::RADIX);

        for ($index = 0; $index < $size; $index++)
            $counts[$this->digit($keys[$index], $byte)]++; // @phpstan-ignore-line

        for ($index = 1; $index < self::RADIX; $index++)
            $counts[$index] += $counts[$index - 1]; // @phpstan-ignore-line

        $output_values = []; $output_keys = [];
        for ($index = $size - 1; $index >= 0; $index--) {

            $digit = $this->digit($keys[$index], $byte); // @phpstan-ignore offsetAccess.notFound
            $position = --$counts[$digit]; // @phpstan-ignore offsetAccess.notFound

            $output_values[$position] = $values[$index]; // @phpstan-ignore offsetAccess.notFound
            $output_keys[$position] = $keys[$index]; // @phpstan-ignore offsetAccess.notFound

        }

        /**
         * @var array{
         *     array<non-negative-int, TElement>,
         *     array<non-negative-int, int>
         * }
         */
        return [$output_values, $output_keys];

    }

    /**
     * ### Extracts a radix digit
     *
     * Extracts the byte used by the current radix pass. The sign bit is transformed for the most significant byte so
     * that signed integers retain their natural ordering.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\RadixSort::SIGN_MASK To transform the sign bit.
     * @uses \FireHub\Core\Foundation\Constant\Numeric\IntegerLimits::SIZE To determine the number of bytes in an
     * integer.
     *
     * @param int $key <p>
     * Integer key.
     * </p>
     * @param non-negative-int $byte <p>
     * Byte position.
     * </p>
     *
     * @return int<0, 255> Extracted byte.
     */
    private function digit (int $key, int $byte):int {

        $digit = ($key >> ($byte * 8)) & 0xff;

        if ($byte === IntegerLimits::SIZE - 1)
            $digit ^= self::SIGN_MASK;

        /** @var int<0, 255> */
        return $digit;

    }

}