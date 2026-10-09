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

namespace FireHub\Foundation\DataStructure\Query;

use FireHub\Core\Boundary\Type\Enumerable;
use FireHub\Core\Type\Maybe;
use FireHub\Foundation\Maybe\ {
    None, Some
};

/**
 * ### Provides finding operations for data structures
 *
 * Provides reusable query operations for locating values, keys, or elements matching specific conditions within
 * a data structure.
 *
 * The query performs non-mutating lookup and positional discovery operations while preserving the underlying data
 * structure and its iteration order.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
final readonly class Find {

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
     * ### Finds the first matching value
     *
     * Finds the first value that satisfies the specified predicate.
     *
     * Iteration stops immediately after the first matching value is found.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey):bool $predicate <p>
     * Predicate used to test each value and its key.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TValue> Matching value if found, otherwise none.
     */
    public function firstWhere (callable $predicate):Maybe {

        foreach ($this->enumerable as $key => $value)
            if ($predicate($value, $key))
                return new Some($value);

        return new None;

    }

    /**
     * ### Finds the last matching value
     *
     * Finds the last value that satisfies the specified predicate.
     *
     * The enumerable is fully traversed to determine the last matching value.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey):bool $predicate <p>
     * Predicate used to test each value and its key.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TValue> Matching value if found, otherwise none.
     */
    public function lastWhere (callable $predicate):Maybe {

        $found = false; $result = null;
        foreach ($this->enumerable as $key => $value) {

            if (!$predicate($value, $key))
                continue;

            $found = true;
            $result = $value;

        }

        /** @var TValue $result */
        return $found
            ? new Some($result)
            : new None;

    }

    /**
     * ### Finds the first key associated with a value
     *
     * Finds the key of the first value identical to the specified value.
     *
     * Iteration stops immediately after the first matching value is found.
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * Value whose key to find.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TKey> Key associated with the first matching value if found, otherwise none.
     */
    public function keyOf (mixed $value):Maybe {

        foreach ($this->enumerable as $key => $stored_value)
            if ($stored_value === $value)
                return new Some($key);

        return new None;

    }

    /**
     * ### Finds the last key associated with a value
     *
     * Finds the key of the last value identical to the specified value.
     *
     * The enumerable is fully traversed to determine the last matching key.
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * Value whose key to find.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TKey> Key associated with the last matching value if found, otherwise none.
     */
    public function lastKeyOf (mixed $value):Maybe {

        $found = false; $result = null;
        foreach ($this->enumerable as $key => $stored_value) {

            if ($stored_value !== $value)
                continue;

            $found = true;
            $result = $key;

        }

        /** @var TKey $result */
        return $found
            ? new Some($result)
            : new None;

    }

    /**
     * ### Finds the value before a specified value
     *
     * Finds the value immediately preceding the first value identical to the specified value in iteration order.
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * Value whose preceding value to find.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TValue> Preceding value if one exists, otherwise none.
     */
    public function beforeValue (mixed $value):Maybe {

        $has_previous = false; $previous = null;
        foreach ($this->enumerable as $stored_value) {

            if ($stored_value === $value) {

                /** @var TValue $previous */
                return $has_previous
                    ? new Some($previous)
                    : new None;

            }

            $has_previous = true;
            $previous = $stored_value;

        }

        return new None;

    }

    /**
     * ### Finds the value after a specified value
     *
     * Finds the value immediately following the first value identical to the specified value in iteration order.
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * Value whose following value to find.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TValue> Following value if one exists, otherwise none.
     */
    public function afterValue (mixed $value):Maybe {

        $matched = false;
        foreach ($this->enumerable as $stored_value) {

            if ($matched)
                return new Some($stored_value);

            if ($stored_value === $value)
                $matched = true;

        }

        return new None;

    }

    /**
     * ### Finds the value before a matching element
     *
     * Finds the value immediately preceding the first element that satisfies the specified predicate in iteration
     * order.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey):bool $predicate <p>
     * Predicate used to identify the target element.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TValue> Preceding value if one exists, otherwise none.
     */
    public function beforeWhere (callable $predicate):Maybe {

        $has_previous = false; $previous = null;
        foreach ($this->enumerable as $key => $value) {

            if ($predicate($value, $key)) {

                /** @var TValue $previous */
                return $has_previous
                    ? new Some($previous)
                    : new None;

            }

            $has_previous = true;
            $previous = $value;

        }

        return new None;

    }

    /**
     * ### Finds the value after a matching element
     *
     * Finds the value immediately following the first element that satisfies the specified predicate in iteration
     * order.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey):bool $predicate <p>
     * Predicate used to identify the target element.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TValue> Following value if one exists, otherwise none.
     */
    public function afterWhere (callable $predicate):Maybe {

        $matched = false;

        foreach ($this->enumerable as $key => $value) {

            if ($matched)
                return new Some($value);

            if ($predicate($value, $key))
                $matched = true;

        }

        return new None;

    }

}