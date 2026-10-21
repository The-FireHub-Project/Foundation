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

use FireHub\Foundation\DataStructure\Stream;

/**
 * ### Splittable data structure
 *
 * Defines a data structure that can partition its elements into consecutive segments according to dynamically
 * determined separators.
 *
 * Each generated segment preserves the concrete type and semantics of the source data structure, while the resulting
 * segments are exposed as a lazy stream.
 *
 * A separator is determined by evaluating a callback for each source element. When the callback evaluates to true,
 * the current element terminates the current segment and is excluded from the generated segments.
 *
 * This boundary defines the primitive splitting operation used by higher-level Foundation transformations.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Splittable {

    /**
     * ### Splits elements by dynamically determined separators
     *
     * Lazily partitions the source elements into consecutive segments according to separators determined by the
     * supplied callback.
     *
     * Elements for which the callback evaluates to true are treated as separators and excluded from the generated
     * segments.
     *
     * Each generated segment preserves the concrete type and semantics of the source data structure.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines whether the current element acts as a separator.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, static> A lazy stream containing the generated segments.
     */
    public function splitBy (callable $callback):Stream;

}