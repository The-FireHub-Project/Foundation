<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Storage\Hash;

/**
 * ### Defines hashing and equality strategy for hash storage
 *
 * Hash strategy provides the mechanisms used by hash-based storage implementations to calculate hashes and
 * determine whether two values represent the same logical value.
 *
 * The strategy separates hashing and equality semantics from the underlying hash storage implementation, allowing
 * different value types to define how they are distributed into hash buckets and how exact equality is determined
 * within those buckets.
 *
 * Implementations must guarantee that values considered equal produce the same hash. Values that produce the same
 * hash are not required to be equal, as hash collisions are permitted and must be resolved through equality
 * comparison.
 *
 * Equality must define an equivalence relation and therefore be reflexive, symmetric, and transitive for all
 * values supported by the implementation.
 * @since 1.0.0
 *
 * @template TValue
 */
interface Strategy {

    /**
     * ### Calculates the hash of a value
     *
     * Calculates a deterministic hash used by hash-based storage implementations to identify the bucket associated
     * with the specified value.
     *
     * Equal values must produce the same hash. Different values may produce the same hash and must therefore be
     * distinguished through equality comparison.
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * The value to hash.
     * </p>
     *
     * @return non-empty-string The calculated hash.
     */
    public function hash (mixed $value):string;

    /**
     * ### Determines whether two values are equal
     *
     * Compares the specified values and determines whether they represent the same logical value according to the
     * equality semantics defined by the strategy.
     * @since 1.0.0
     *
     * @param TValue $left <p>
     * The first value to compare.
     * </p>
     *
     * @param TValue $right <p>
     * The second value to compare.
     * </p>
     *
     * @return bool True if the values are equal, false otherwise.
     */
    public function equals (mixed $left, mixed $right):bool;

}