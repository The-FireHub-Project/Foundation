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
 * ### Sliceable capability
 *
 * Defines a type that can extract a consecutive positional range of its elements without modifying the source.
 *
 * The slice begins at the specified positional offset. A non-negative offset is measured from the beginning of the
 * source, while a negative offset is measured from the end.
 *
 * When a length is specified, the slice contains at most that many elements. When the length is null, all remaining
 * elements from the resolved offset to the end of the source are included. A length of zero produces an empty slice.
 *
 * The offset represents a position within the source iteration order rather than an element key.
 *
 * The extracted range is returned as a new instance, preserving the concrete type and semantics of the source.
 *
 * This capability defines the primitive slicing operation that implementations may optimize using their underlying
 * storage representation.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Sliceable {

    /**
     * ### Extracts a consecutive positional range
     *
     * Creates a new instance containing a consecutive range of elements beginning at the specified positional offset
     * without modifying the current instance.
     *
     * A non-negative offset is measured from the beginning of the source. A negative offset is measured from the end,
     * where -1 represents the last element, -2 the second-to-last element, and so on.
     *
     * When length is specified, at most that many elements are included. When length is null, all remaining elements
     * from the resolved offset to the end of the source are included. A length of zero returns an empty instance.
     *
     * If a positive offset exceeds the number of available elements, an empty instance is returned. If a negative
     * offset exceeds the number of available elements, the slice begins at the first element.
     * @since 1.0.0
     *
     * @param int $offset <p>
     * Positional offset at which the slice begins. A non-negative value is measured from the beginning of the source,
     * while a negative value is measured from the end.
     * </p>
     * @param null|non-negative-int $length [optional] <p>
     * Maximum number of elements to include, or null to include all remaining elements from the resolved offset.
     * </p>
     *
     * @return static<TKey, TValue> A new instance containing the extracted range.
     */
    public function slice (int $offset, ?int $length = null):static;

}