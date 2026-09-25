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
 * ### Takeable data structure
 *
 * Defines a data structure capable of taking consecutive elements from the beginning of its iteration sequence while
 * a specified condition evaluates to true.
 *
 * Taking always begins with the first element exposed by the data structure and terminates immediately when the
 * callback evaluates to false. The element for which the callback first evaluates to false is not included in the
 * resulting data structure.
 *
 * The resulting data structure preserves the concrete type and semantics of the source.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Takeable {

    /**
     * ### Takes values while condition is satisfied
     *
     * Creates a new data structure containing consecutive values from the beginning of the source while the callback
     * evaluates to true.
     *
     * Evaluation stops when the callback first evaluates to false. The matching value and all later values are
     * excluded from the result.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines whether the current value should be included in the result.
     * </p>
     *
     * @return static The resulting data structure.
     */
    public function takeWhile (callable $callback):static;

}