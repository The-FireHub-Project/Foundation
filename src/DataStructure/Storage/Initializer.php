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

namespace FireHub\Foundation\DataStructure\Storage;

/**
 * ### Defines a strategy for providing the initial contents of a storage
 *
 * An initializer supplies the values from which a storage establishes its initial state. It abstracts the source
 * and preparation of initial data from the storage implementation, allowing the same initialization strategy to
 * be used with different storage implementations.
 *
 * An initializer does not define how values are stored, accessed, traversed, or modified after initialization.
 * The consuming storage is responsible for materializing and managing the supplied contents, according to its own
 * storage semantics.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Initializer {

    /**
     * ### Returns iterable of key-value pairs representing the initial contents of the storage
     * @since 1.0.0
     *
     * @return iterable<TKey, TValue> The iterable of key-value pairs representing the initial contents of the storage.
     */
    public function initialize ():iterable;

}