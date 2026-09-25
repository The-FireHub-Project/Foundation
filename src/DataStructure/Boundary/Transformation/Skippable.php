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
 * ### Skippable data structure
 *
 * Defines a data structure capable of skipping consecutive elements from the beginning of its iteration sequence
 * while a specified condition evaluates to true.
 *
 * Skipping always begins with the first element exposed by the data structure and terminates immediately when the
 * callback evaluates to false. The first element for which the callback evaluates to false and all later
 * elements are included in the resulting data structure.
 *
 * The resulting data structure preserves the concrete type and semantics of the source.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Skippable {

    /**
     * ### Skips values while condition is satisfied
     *
     * Creates a new data structure by excluding consecutive values from the beginning of the source while the
     * callback evaluates to true.
     *
     * Skipping stops when the callback first evaluates to false. That value and all later values are included in
     * the result without further callback evaluation being required.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines whether the current value should continue to be skipped.
     * </p>
     *
     * @return static The resulting data structure.
     */
    public function skipWhile (callable $callback):static;

}