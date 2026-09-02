<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.0
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Storage\Hash;

/**
 * ### Defines the fundamental contract for a hash storage engine
 *
 * A hash engine provides the underlying mechanism for storing, accessing, modifying, and traversing key-value
 * pairs used by a hash-based storage implementation. It abstracts the concrete representation and manipulation
 * of hash data from the storage that consumes it.
 *
 * The engine is responsible for maintaining the hash representation and its associated operations, while the
 * consuming storage defines the public data structure semantics exposed to its users.
 *
 * Specialized hash engine contracts may extend this contract to provide additional capabilities such as key
 * lookup, mutation, removal, or size management.
 * @since 1.0.0
 *
 * @template TKey of int|string
 * @template TValue
 */
interface Engine {

    /**
     * ### Returns iterable for traversing the stored key-value pairs
     * @since 1.0.0
     *
     * @return iterable<TKey, TValue> The stored key-value pairs.
     */
    public function iterate ():iterable;

    /**
     * ### Gets the number of elements stored in the hash engine
     * @since 1.0.0
     *
     * @return non-negative-int The number of elements stored in the hash engine.
     */
    public function size ():int;

}