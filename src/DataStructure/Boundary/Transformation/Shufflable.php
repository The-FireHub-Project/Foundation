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

namespace FireHub\Foundation\DataStructure\Boundary\Transformation;

/**
 * ### Shufflable data structure
 *
 * Defines a data structure whose canonical iteration order can be randomly rearranged.
 *
 * Shuffling produces a new data structure containing the same elements as the source while exposing them in a
 * randomized canonical iteration order. The source data structure remains unchanged.
 *
 * The concrete implementation determines how keys and positional indexes are handled according to the semantics of
 * the data structure. Positional structures may reindex their elements, while associative structures preserve
 * key-value associations.
 *
 * This boundary defines the primitive shuffle operation used by higher-level Foundation transformations that depend
 * on randomized iteration order.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Shufflable {

    /**
     * ### Randomizes the canonical iteration order
     *
     * Creates a new data structure containing the same elements as the source in a randomized canonical iteration
     * order.
     *
     * The source data structure remains unchanged. Key and index handling follows the semantics of the concrete data
     * structure.
     * @since 1.0.0
     *
     * @return static<TKey, TValue> A new data structure with randomized canonical iteration order.
     */
    public function shuffle ():static;

}