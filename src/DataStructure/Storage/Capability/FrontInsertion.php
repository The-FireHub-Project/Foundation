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
 * ### Defines the ability to insert values at the front of a storage
 *
 * Front insertion defines the ability to insert a value at the front boundary of a storage. It provides the
 * operation required to add a value before all values currently contained in the storage.
 *
 * The capability defines only the insertion behavior and does not specify how values are represented, organized,
 * or stored internally.
 * @since 1.0.0
 *
 * @template TValue
 */
interface FrontInsertion {

    /**
     * ### Inserts values at the front of the storage
     *
     * Inserts one or more values at the front boundary of the storage as a single batch operation. The values are
     * inserted in the same order in which they are provided, so the first value becomes the first newly inserted
     * value.
     * @since 1.0.0
     *
     * @param TValue ...$values <p>
     * The values to insert at the front of the storage.
     * </p>
     *
     * @return void
     */
    public function insertFront (mixed ...$values):void;

}