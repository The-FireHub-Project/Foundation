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
 * ### Defines access to the boundary values of a linear storage
 *
 * Linear boundary access provides read-only access to the first and last values currently contained in a linear
 * storage.
 *
 * The capability does not define how boundary values are stored or modified. It only defines access to the
 * values at the beginning and end of the linear sequence.
 * @since 1.0.0
 *
 * @template TValue
 */
interface LinearBoundaryAccess {

    /**
     * ### Returns the first value in the storage
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The first value, or an empty maybe if the storage contains no
     * values.
     */
    public function first ():Maybe;

    /**
     * ### Returns the last value in the storage
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The last value, or an empty maybe if the storage contains no
     * values.
     */
    public function last ():Maybe;

}
