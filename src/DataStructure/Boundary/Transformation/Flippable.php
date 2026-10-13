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
 * ### Flippable data structure
 *
 * Defines a data structure that can exchange its keys with their corresponding values.
 *
 * Each source value becomes a key in the resulting map, while its corresponding source key becomes the associated
 * value.
 *
 * Source values must be valid array keys and are therefore limited to integers and strings.
 *
 * When multiple source elements contain the same value, the later element replaces the earlier one because duplicate
 * values become duplicate keys in the resulting map.
 *
 * The source data structure remains unchanged by the operation.
 *
 * This boundary defines the primitive flipping operation used by higher-level Foundation data structures and allows
 * implementations to provide optimized flipping behavior.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Flippable {

    /**
     * ### Exchanges keys with values
     *
     * Creates a map in which each source value becomes a key and each corresponding source key becomes its associated
     * value.
     *
     * When the same value occurs more than once in the source, only the last corresponding key is preserved in the
     * resulting map.
     *
     * The current data structure remains unchanged.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Map<TValue, TKey> A map containing the source values as keys and the
     * corresponding source keys as values.
     */
    public function flip ():Map;

}