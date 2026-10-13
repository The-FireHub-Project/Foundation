<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.1
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Stream;

/**
 * ### Defines a source of Stream elements
 *
 * A Source represents the origin of elements consumed by a Stream.
 *
 * Sources expose key-value pairs through iteration without requiring the complete sequence to be materialized in
 * memory. Elements may originate from the existing iterable, be generated dynamically, retrieved from an external
 * resource, or be produced by another Source as part of a Stream processing pipeline.
 *
 * The Source contract defines only how elements are produced. It does not prescribe whether the source is finite,
 * replayable, stateful, materialized, or consumable only once. These characteristics are determined by the concrete
 * implementation and its underlying data provider.
 *
 * Source implementations may also decorate other Sources to perform lazy transformations while preserving the
 * pull-based execution model of a Stream.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Source {

    /**
     * ### Iterates over the source elements
     *
     * Produces the key-value pairs represented by this Source.
     *
     * Values are produced as they are requested by the consumer, and implementations are not required to materialize
     * the complete sequence before iteration begins.
     * @since 1.0.0
     *
     * @return iterable<TKey, TValue> The elements produced by the source.
     */
    public function iterate ():iterable;

}