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
 * ### Defines combinable data structure capabilities
 *
 * Defines the contract for data structures whose values can be combined with values from another iterable to
 * create a map.
 *
 * Values from the current data structure become keys of the resulting map, while values from the provided iterable
 * become their corresponding values.
 *
 * Both sources must contain the same number of elements.
 * @since 1.0.0
 *
 * @template TKey
 */
interface Combinable {

    /**
     * ### Combines values into a map
     *
     * Combines values from the current data structure with values from the provided iterable, using values from the
     * current data structure as keys of the resulting map.
     * @since 1.0.0
     *
     * @template TValue
     *
     * @param iterable<TValue> $values <p>
     * Values to associate with the values of the current data structure.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Map<TKey, TValue> Map containing the combined keys and values.
     */
    public function combine (iterable $values):Map;

}