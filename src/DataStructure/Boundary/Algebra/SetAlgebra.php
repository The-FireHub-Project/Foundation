<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Boundary\Algebra;

/**
 * ### Defines set algebra operations
 *
 * Defines algebraic operations for set-like data structures.
 *
 * Set algebra operates on value membership rather than positional or key-based semantics. Implementations determine
 * value equality according to their underlying storage and equality strategy.
 *
 * All operations produce a new set-like structure and do not modify the current structure.
 * @since 1.0.0
 *
 * @template TValue
 */
interface SetAlgebra {

    /**
     * ### Creates the union
     *
     * Produces a new set containing every value that exists in either the current set or the provided values.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to combine with the current set.
     * </p>
     *
     * @return static A new set containing the union of both value collections.
     */
    public function union (iterable $values):static;

    /**
     * ### Creates the intersection
     *
     * Produces a new set containing only values that exist in both the current set and the provided values.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to intersect with the current set.
     * </p>
     *
     * @return static A new set containing values common to both value collections.
     */
    public function intersection (iterable $values):static;

    /**
     * ### Creates the difference
     *
     * Produces a new set containing values that exist in the current set but do not exist in the provided values.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to subtract from the current set.
     * </p>
     *
     * @return static A new set containing the difference between both value collections.
     */
    public function difference (iterable $values):static;

    /**
     * ### Creates the symmetric difference
     *
     * Produces a new set containing values that exist in exactly one of the current set or the provided values, but
     * not in both.
     * @since 1.0.0
     *
     * @param iterable<TValue> $values <p>
     * Values to compare with the current set.
     * </p>
     *
     * @return static A new set containing the symmetric difference between both value collections.
     */
    public function symmetricDifference (iterable $values):static;

}