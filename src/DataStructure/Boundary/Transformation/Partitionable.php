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

namespace FireHub\Foundation\DataStructure\Boundary\Transformation;

use FireHub\Foundation\DataStructure\Tuple;

/**
 * ### Partitionable data structure
 *
 * Defines a data structure whose elements can be partitioned into two groups according to a dynamically evaluated
 * predicate.
 *
 * Elements for which the predicate evaluates to true are placed in the first partition, while elements for which
 * the predicate evaluates to false are placed in the second partition.
 *
 * Each generated partition preserves the concrete type and semantics of the source data structure, as well as the
 * encounter order of its elements according to the canonical iteration order of the source.
 *
 * Every source element belongs to exactly one of the generated partitions.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Partitionable {

    /**
     * ### Partitions elements by predicate
     *
     * Partitions the source elements into matching and non-matching groups according to the supplied predicate.
     *
     * The first tuple element contains values for which the predicate evaluates to true, while the second contains
     * values for which the predicate evaluates to false.
     *
     * The predicate is evaluated exactly once for each source element.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines the partition to which the current element belongs.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Tuple<static> A tuple containing the matching partition followed by
     * the non-matching partition.
     */
    public function partition (callable $callback):Tuple;

}