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
use FireHub\Runtime;

/**
 * ### Bucket sort algorithm
 *
 * Implements the bucket sort non-comparison sorting algorithm for numeric keys.
 *
 * The algorithm distributes elements into buckets according to their relative position within the observed numeric
 * key range. Elements within individual buckets are then ordered by their numeric keys before the buckets are combined
 * into the resulting sequence.
 *
 * By default, the number of buckets is derived from the number of elements using the square root of the input size.
 * A fixed number of buckets may be supplied when explicit control over the distribution strategy is required.
 *
 * ##### Recommended usage
 *
 * Bucket sort is suitable for numeric data reasonably uniformly distributed across its key range.
 *
 * It can perform particularly well for floating-point values and other numeric values whose distribution allows
 * elements to be spread relatively evenly across buckets.
 *
 * It should generally not be used when values are heavily clustered because a disproportionate number of elements may
 * be placed into the same bucket, reducing the benefits of the distribution phase.
 *
 * The average performance depends on how evenly elements are distributed between buckets. The algorithm requires
 * O(n + b) additional storage, where n is the number of elements and b is the number of buckets.
 * @since 1.0.0
 *
 * @template TElement
 *
 * @implements \FireHub\Core\Boundary\Algorithm\Sorting\DistributionSortAlgorithm<TElement, int|float>
 */
final readonly class BucketSort implements DistributionSortAlgorithm {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param null|positive-int $buckets <p>
     * Number of buckets used to distribute elements, or null to determine the number automatically from the input
     * size.
     * </p>
     */
    public function __construct (
        private ?int $buckets = null
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\BucketSort::extract() To extract values and their
     * numeric keys.
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\BucketSort::bucketCount() To determine the number of
     * buckets.
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\BucketSort::distribute() To distribute element indexes
     * across buckets.
     * @uses \FireHub\Foundation\Algorithm\Sorting\Distribution\BucketSort::write() To order and write bucket values.
     */
    public function sort (int $size, callable $element, callable $set, callable $key):void {

        if ($size < 2) return;

        [$values, $keys, $minimum, $maximum] = $this->extract(
            $size, $element, $key
        );

        if ($minimum === $maximum)
            return;

        $buckets = $this->distribute($size, $this->bucketCount($size), $keys, $minimum, $maximum);

        $this->write($buckets, $values, $keys, $set);

    }

    /**
     * ### Extracts values and keys
     *
     * Reads all elements, extracts their numeric sorting keys, and determines the minimum and maximum key values.
     * @since 1.0.0
     *
     * @param positive-int $size <p>
     * Number of elements.
     * </p>
     * @param callable(non-negative-int):TElement $element <p>
     * Element accessor.
     * </p>
     * @param callable(TElement):(int|float) $key <p>
     * Numeric key extractor.
     * </p>
     *
     * @return array{
     *     list<TElement>,
     *     list<int|float>,
     *     int|float,
     *     int|float
     * } Extracted values, keys, minimum, and maximum key values.
     */
    private function extract (int $size, callable $element, callable $key):array {

        $values = [];
        $keys = [];

        $first = $element(0);
        $first_key = $key($first);

        $values[] = $first;
        $keys[] = $first_key;

        $minimum = $first_key;
        $maximum = $first_key;

        for ($index = 1; $index < $size; $index++) {

            $value = $element($index);
            $current = $key($value);

            $values[] = $value;
            $keys[] = $current;

            if ($current < $minimum)
                $minimum = $current;

            if ($current > $maximum)
                $maximum = $current;

        }

        return [$values, $keys, $minimum, $maximum];

    }

    /**
     * ### Determines the number of buckets
     *
     * Returns the explicitly configured number of buckets when provided. Otherwise, derives the bucket count from the
     * square root of the number of elements.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::max() To ensure a non-negative bucket count.
     * @uses \FireHub\Runtime\Math::sqrt() To calculate the square root of the input size.
     *
     * @param positive-int $size <p>
     * Number of elements.
     * </p>
     *
     * @return positive-int Number of buckets.
     */
    private function bucketCount (int $size):int {

        if ($this->buckets !== null)
            return $this->buckets;

        /** @var positive-int */
        return Runtime\Math::max(1, (int) Runtime\Math::sqrt($size));

    }

    /**
     * ### Distributes elements into buckets
     *
     * Distributes element indexes across buckets according to the relative position of their numeric keys within the
     * observed key range.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Structure::fill() To initialize the bucket array.
     *
     * @param positive-int $size <p>
     * Number of elements.
     * </p>
     * @param positive-int $bucket_count <p>
     * Number of buckets.
     * </p>
     * @param list<int|float> $keys <p>
     * Numeric sorting keys.
     * </p>
     * @param int|float $minimum <p>
     * Minimum numeric key.
     * </p>
     * @param int|float $maximum <p>
     * Maximum numeric key.
     * </p>
     *
     * @return list<list<non-negative-int>> Distributed element indexes.
     */
    private function distribute (int $size, int $bucket_count, array $keys, int|float $minimum, int|float $maximum):array {

        /** @var list<list<non-negative-int>> $buckets */
        $buckets = Runtime\Arr\Structure::fill( // @phpstan-ignore varTag.type
            [], 0, $bucket_count
        );

        $range = $maximum - $minimum;

        for ($index = 0; $index < $size; $index++) {

            $normalized = ($keys[$index] - $minimum) / $range; // @phpstan-ignore offsetAccess.notFound

            $bucket = (int) ($normalized * $bucket_count);

            if ($bucket === $bucket_count)
                $bucket--;

            $buckets[$bucket][] = $index;

        }

        return $buckets;

    }

    /**
     * ### Writes ordered bucket values
     *
     * Orders indexes within individual buckets according to their numeric keys and writes the corresponding values
     * into their resulting positions.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Ordering::sortBy() To sort bucket indexes by numeric keys.
     *
     * @param list<list<non-negative-int>> $buckets <p>
     * Distributed element indexes.
     * </p>
     * @param list<TElement> $values <p>
     * Values being sorted.
     * </p>
     * @param list<int|float> $keys <p>
     * Numeric sorting keys.
     * </p>
     * @param callable(non-negative-int, TElement):void $set <p>
     * Element mutator.
     * </p>
     *
     * @return void
     */
    private function write (array $buckets, array $values, array $keys, callable $set):void {

        $position = 0;

        foreach ($buckets as $bucket) {

            if ($bucket === [])
                continue;

            Runtime\Arr\Ordering::sortBy(
                $bucket,
                static fn (int $first, int $second):int =>
                    $keys[$first] <=> $keys[$second] // @phpstan-ignore-line
            );

            foreach ($bucket as $index)
                $set($position++, $values[$index]); // @phpstan-ignore offsetAccess.notFound

        }

    }

}