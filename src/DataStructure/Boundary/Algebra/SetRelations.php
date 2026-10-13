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

namespace FireHub\Foundation\DataStructure\Boundary\Algebra;

/**
 * ### Defines set relation operations
 *
 * Defines relational operations for determining mathematical relationships between a set and another collection of
 * values.
 *
 * Set relations operate on value membership rather than positional or key-based semantics. Implementations determine
 * value equality according to their underlying storage and equality strategy.
 * @since 1.0.0
 *
 * @template TValue
 */
interface SetRelations {

    /**
     * ### Determines whether this set is a subset
     *
     * Determines whether every value contained in the current set is also contained in the provided values.
     * Equality between both sets is permitted.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to compare with the current set.
     * </p>
     *
     * @return bool True if the current set is a subset of the provided values, otherwise false.
     */
    public function isSubsetOf (iterable $values):bool;

    /**
     * ### Determines whether this set is a proper subset
     *
     * Determines whether every value contained in the current set is also contained in the provided values while the
     * provided values contain at least one additional distinct value.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to compare with the current set.
     * </p>
     *
     * @return bool True if the current set is a proper subset of the provided values, otherwise false.
     */
    public function isProperSubsetOf (iterable $values):bool;

    /**
     * ### Determines whether this set is a superset
     *
     * Determines whether every value provided by the specified iterable is contained in the current set. Equality
     * between both sets is permitted.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to compare with the current set.
     * </p>
     *
     * @return bool True if the current set is a superset of the provided values, otherwise false.
     */
    public function isSupersetOf (iterable $values):bool;

    /**
     * ### Determines whether this set is a proper superset
     *
     * Determines whether every value provided by the specified iterable is contained in the current set while the
     * current set contains at least one additional distinct value.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to compare with the current set.
     * </p>
     *
     * @return bool True if the current set is a proper superset of the provided values, otherwise false.
     */
    public function isProperSupersetOf (iterable $values):bool;

    /**
     * ### Determines whether the sets are disjoint
     *
     * Determines whether the current set and the provided values have no values in common.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to compare with the current set.
     * </p>
     *
     * @return bool True if both value collections are disjoint, otherwise false.
     */
    public function isDisjointWith (iterable $values):bool;

}