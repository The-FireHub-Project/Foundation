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

use FireHub\Core\Type\Maybe;

/**
 * ### Defines indexed key-based access to stored values
 *
 * Key access provides read-only access to values identified by their keys. It allows callers to determine whether
 * a value exists for a given key and retrieve the associated value without modifying the storage.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface KeyAccess {

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

}