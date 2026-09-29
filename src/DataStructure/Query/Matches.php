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

namespace FireHub\Foundation\DataStructure\Query;

use FireHub\Core\Boundary\Type\Enumerable;

/**
 * ### Provides matching operations for data structures
 *
 * Provides reusable query operations for determining whether values, key-value pairs, or elements matching
 * specific conditions exist within a data structure.
 *
 * The query performs non-mutating membership and predicate-based checks while preserving the underlying data
 * structure and its iteration order.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
final readonly class Matches {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\Type\Enumerable<TKey, TValue> $enumerable <p>
     * Enumerable data structure to query.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Enumerable $enumerable
    ) {}

    /**
     * ### Determines whether a value exists
     *
     * Determines whether the enumerable contains a value identical to the specified value.
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * Value to search for.
     * </p>
     *
     * @return bool True if the value exists, false otherwise.
     */
    public function value (mixed $value):bool {

        foreach ($this->enumerable as $stored_value)
            if ($stored_value === $value)
                return true;

        return false;

    }

    /**
     * ### Determines whether a key-value pair exists
     *
     * Determines whether the enumerable contains a key-value pair identical to the specified key and value.
     * @since 1.0.0
     *
     * @param TKey $key <p>
     * Key to search for.
     * </p>
     * @param TValue $value <p>
     * Value associated with the key.
     * </p>
     *
     * @return bool True if the key-value pair exists, false otherwise.
     */
    public function pair (mixed $key, mixed $value):bool {

        foreach ($this->enumerable as $stored_key => $stored_value)
            if ($stored_key === $key && $stored_value === $value)
                return true;

        return false;

    }

    /**
     * ### Determines whether an element matches a condition
     *
     * Determines whether at least one element in the enumerable satisfies the specified predicate.
     *
     * Iteration stops immediately after the first matching element is found.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey):bool $predicate <p>
     * Predicate used to test each value and its key.
     * </p>
     *
     * @return bool True if at least one element satisfies the predicate, false otherwise.
     */
    public function where (callable $predicate):bool {

        foreach ($this->enumerable as $key => $value)
            if ($predicate($value, $key))
                return true;

        return false;


    }

    /**
     * ### Determines whether any specified value exists
     *
     * Determines whether the enumerable contains at least one value from the specified collection of values.
     *
     * The operation returns immediately after the first matching value is found.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to search for.
     * </p>
     *
     * @return bool True if at least one specified value exists, false otherwise.
     */
    public function anyOf (iterable $values):bool {

        foreach ($values as $expected)
            if ($this->value($expected))
                return true;

        return false;

    }

    /**
     * ### Determines whether all specified values exist
     *
     * Determines whether the enumerable contains every value from the specified collection of values.
     *
     * The operation returns immediately after the first missing value is encountered.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to search for.
     * </p>
     *
     * @return bool True if every specified value exists, false otherwise.
     */
    public function allOf (iterable $values):bool {

        foreach ($values as $expected)
            if (!$this->value($expected))
                return false;

        return true;

    }

}