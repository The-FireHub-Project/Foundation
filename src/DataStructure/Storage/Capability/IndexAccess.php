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
 * ### Defines indexed access to values in a storage
 *
 * Indexed access provides read-only access to values by their integer index. The capability defines how position
 * can retrieve values from a storage without exposing its underlying storage representation.
 * @since 1.0.0
 *
 * @template TValue
 */
interface IndexAccess {

    /**
     * ### Determines whether an index exists
     *
     * Determines whether the specified index currently exists in the storage.
     * @since 1.0.0
     *
     * @param int $index <p>
     * The index to check.
     * </p>
     *
     * @return bool True if the index exists, false otherwise.
     */
    public function has (int $index):bool;

    /**
     * ### Returns the value at the specified index
     *
     * Returns the value currently stored at the specified index.
     * @since 1.0.0
     *
     * @param int $index <p>
     * The zero-based index of the value to retrieve.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The value at the specified index, or an empty Maybe if the index
     * does not exist.
     */
    public function get (int $index):Maybe;

}