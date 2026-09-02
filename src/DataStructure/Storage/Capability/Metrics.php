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
 * ### Defines the ability to provide storage metrics
 *
 * Storage metrics provide information about the current state of a storage without defining how the stored values
 * are represented, accessed, or modified.
 *
 * The capability defines only metrics that are generally applicable to storage implementations. Specialized
 * capabilities may provide additional information about specific storage characteristics.
 * @since 1.0.0
 */
interface Metrics {

    /**
     * ### Checks if the storage is empty
     * @since 1.0.0
     *
     * @return bool Returns true if the storage is empty, false otherwise.
     */
    public function isEmpty ():bool;

    /**
     * ### Gets the number of elements in the storage
     * @since 1.0.0
     *
     * @return non-negative-int The number of elements in the storage.
     */
    public function size ():int;

}