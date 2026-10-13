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
 * ### Spliceable capability
 *
 * Defines a type that can remove a consecutive positional range of its elements and optionally replace the removed
 * range with another sequence of values.
 *
 * The range begins at the specified positional offset. A non-negative offset is measured from the beginning of the
 * source, while a negative offset is measured from the end.
 *
 * When a length is specified, at most that many elements are removed. When the length is null, all remaining elements
 * from the resolved offset to the end of the source are removed. A length of zero removes no elements and causes the
 * replacement values to be inserted at the resolved offset.
 *
 * The offset represents a position within the source iteration order rather than an element key.
 *
 * The operation modifies the current instance, while the removed elements are returned as a separate instance
 * preserving the concrete type and semantics of the source.
 *
 * This capability defines the primitive splicing operation that implementations may optimize using their underlying
 * storage representation.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Spliceable {

    /**
     * ### Removes and optionally replaces a consecutive positional range
     *
     * Removes a consecutive range of elements beginning at the specified positional offset and optionally inserts the
     * replacement values at the position where the removed range begins.
     *
     * A non-negative offset is measured from the beginning of the source. A negative offset is measured from the end,
     * where -1 represents the last element, -2 the second-to-last element, and so on.
     *
     * When length is specified, at most that many elements are removed. When length is null, all remaining elements
     * from the resolved offset to the end of the source are removed. A length of zero performs insertion without
     * removing any elements.
     *
     * If a positive offset exceeds the number of available elements, no elements are removed and the replacement
     * values are inserted at the end. If a negative offset exceeds the number of available elements, the operation
     * begins at the first element.
     *
     * Replacement values are inserted according to their iteration order. Their keys, when present, do not determine
     * their position within the resulting instance.
     * @since 1.0.0
     *
     * @template TReplacementKey
     * @template TReplacementValue
     *
     * @param int $offset <p>
     * Positional offset at which removal and replacement begin. A non-negative value is measured from the beginning
     * of the source, while a negative value is measured from the end.
     * </p>
     * @param null|non-negative-int $length [optional] <p>
     * Maximum number of elements to remove, or null to remove all remaining elements from the resolved offset.
     * </p>
     * @param iterable<TReplacementKey, TReplacementValue> $replacement [optional] <p>
     * Sequence of values to insert at the resolved offset in place of the removed range.
     * </p>
     *
     * @return static<TKey, TValue> A new instance containing the removed elements.
     */
    public function splice (int $offset, ?int $length = null, iterable $replacement = []):static;

}