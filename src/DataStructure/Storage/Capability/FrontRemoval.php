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
 * ### Defines the ability to remove values from the front of a storage
 *
 * Front removal defines the ability to remove and return a value from the front boundary of a storage. It provides
 * the operation required to remove the first value currently contained in the storage.
 *
 * The capability defines only the removal behavior and does not specify how values are represented, organized,
 * or stored internally.
 * @since 1.0.0
 *
 * @template TValue
 */
interface FrontRemoval {

    /**
     * ### Removes and returns the first value
     *
     * Removes the value currently located at the front boundary of the storage and returns the removed value.
     * If the storage contains no values, an empty Maybe is returned.
     *
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The removed value, or an empty Maybe if the storage is empty.
     */
    public function removeFront ():Maybe;

}