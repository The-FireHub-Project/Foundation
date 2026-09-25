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

use FireHub\Foundation\DataStructure\Map;

/**
 * ### Groupable data structure
 *
 * Defines a data structure whose elements can be partitioned into groups according to a dynamically determined
 * identity.
 *
 * Elements producing the same group identity belong to the same group regardless of their position in the source.
 * Each generated group preserves the concrete type and semantics of the source data structure.
 *
 * Groups preserve the encounter order of their elements according to the canonical iteration order of the source.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Groupable {

    /**
     * ### Groups elements by identity
     * @since 1.0.0
     *
     * @template TGroup of array-key
     *
     * @param callable(TValue, TKey=):TGroup $selector <p>
     * Callback that determines the identity of the group to which the current element belongs.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Map<TGroup, static> The generated groups indexed by their identities.
     */
    public function groupBy (callable $selector):Map;

}