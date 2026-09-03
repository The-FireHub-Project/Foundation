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

/**
 * ### Defines the ability to insert values at the back of a storage
 *
 * Back insertion defines the ability to insert a value at the back boundary of a storage. It provides the
 * operation required to add a value after all values currently contained in the storage.
 *
 * The capability defines only the insertion behavior and does not specify how values are represented, organized,
 * or stored internally.
 * @since 1.0.0
 *
 * @template TValue
 */
interface BackInsertion {

    /**
     * ### Inserts values at the back of the storage
     *
     * Inserts one or more values at the back boundary of the storage as a single batch operation. The values are
     * inserted in the same order in which they are provided.
     * @since 1.0.0
     *
     * @param TValue ...$values <p>
     * The values to insert at the back of the storage.
     * </p>
     *
     * @return void
     */
    public function insertBack (mixed ...$values):void;

}