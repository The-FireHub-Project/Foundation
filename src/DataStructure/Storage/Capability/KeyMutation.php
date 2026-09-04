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

namespace FireHub\Foundation\DataStructure\Storage\Capability;

use FireHub\Core\Meta\Enum\MutationOutcome;

/**
 * ### Defines key-based mutation of stored values
 *
 * Key mutation provides write access to values identified by their keys. It allows callers to create, update, and
 * remove values associated with keys.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface KeyMutation {

    /**
     * ### Sets a value for the specified key
     *
     * If the key does not exist, a new value is created. If the key already exists, its associated value is updated.
     * @since 1.0.0
     *
     * @param TKey $key <p>
     * The key for the value.
     * </p>
     * @param TValue $value <p>
     * The value to store.
     * </p>
     *
     * @return \FireHub\Core\Meta\Enum\MutationOutcome The outcome of the mutation. CREATED if a new value was created,
     * UPDATED if an existing value was updated, or NOT_FOUND if the key did not exist.
     */
    public function set (mixed $key, mixed $value):MutationOutcome;

    /**
     * ### Removes the value associated with the specified key
     * @since 1.0.0
     *
     * @param TKey $key <p>
     * The key whose associated value should be removed.
     * </p>
     *
     * @return \FireHub\Core\Meta\Enum\MutationOutcome The outcome of the mutation. REMOVED if the key existed and was removed,
     * or NOT_FOUND if the key did not exist.
     */
    public function remove (mixed $key):MutationOutcome;

}