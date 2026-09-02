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

namespace FireHub\Foundation\DataStructure\Storage\Capability;

/**
 * ### Defines the ability to provide a storage capacity
 *
 * Storage capacity describes the maximum number of values that a storage can contain according to its storage
 * strategy.
 *
 * A storage implementation may provide this capability when its capacity is explicitly bounded. Storage without
 * a defined upper bound does not need to implement this capability.
 * @since 1.0.0
 */
interface Capacity {

    /**
     * ### Determines whether the storage has reached its maximum capacity
     * @since 1.0.0
     *
     * @return bool Returns true if the storage has reached its maximum capacity, false otherwise.
     */
    public function isFull ():bool;

    /**
     * ### Gets the maximum number of values that the storage can contain
     * @since 1.0.0
     *
     * @return non-negative-int The maximum number of values that the storage can contain.
     */
    public function capacity ():int;

    /**
     * ### Gets the number of additional values the storage can contain
     * @since 1.0.0
     *
     * @return non-negative-int The number of additional values the storage can contain.
     */
    public function remainingCapacity ():int;

}