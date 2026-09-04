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

use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;

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

    /**
     * ### Determines whether the storage contains a value for the specified key
     * @since 1.0.0
     *
     * @param TKey $key <p>
     * The key to check.
     * </p>
     *
     * @return bool True if a value exists for the specified key, false otherwise.
     */
    public function has (mixed $key):bool;

    /**
     * ### Returns the value associated with the specified key
     * @since 1.0.0
     *
     * @param TKey $key <p>
     * The key whose value should be returned.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The associated value, or an empty Maybe if the key does not exist.
     */
    public function get (mixed $key):Maybe;

    /**
     * ### Sets the value for the specified key
     *
     * Stores the specified value at the specified key. If the key does not exist, a new entry is created. If the
     * key already exists, its value is replaced.
     * @since 1.0.0
     *
     * @param TKey $key <p>
     * The key at which to store the value.
     * </p>
     *
     * @param TValue $value <p>
     * The value to store.
     * </p>
     *
     * @return \FireHub\Core\Meta\Enum\MutationOutcome The outcome of the mutation: CREATED if a new entry was
     * created or UPDATED if an existing entry was updated.
     */
    public function set (mixed $key, mixed $value):MutationOutcome;

    /**
     * ### Removes the value for the specified key
     *
     * Removes the value currently stored for the specified key. If the key does not exist, no mutation is
     * performed.
     * @since 1.0.0
     *
     * @param TKey $key <p>
     * The key of the value to remove.
     * </p>
     *
     * @return \FireHub\Core\Meta\Enum\MutationOutcome The outcome of the mutation: REMOVED if an entry was removed,
     * or NOT_FOUND if the index does not exist.
     */
    public function remove (mixed $key):MutationOutcome;

}