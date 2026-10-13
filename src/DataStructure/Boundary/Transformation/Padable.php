<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Boundary\Transformation;

use FireHub\Core\Meta\Enum\Side;

/**
 * ### Padable data structure
 *
 * Defines a data structure that can be extended to a specified size by adding a value to one of its boundaries.
 *
 * Padding produces a new data structure and leaves the source unchanged. If the requested size does not exceed the
 * current size, an equivalent data structure is returned without adding values.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Padable {

    /**
     * ### Pads the data structure to the specified size
     *
     * Extends the data structure to the requested size by repeatedly adding the specified value to the selected
     * boundary.
     * @since 1.0.0
     *
     * @param non-negative-int $size <p>
     * Target size of the data structure.
     * </p>
     * @param TValue $value <p>
     * Value used to pad the data structure.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Side $side [optional] <p>
     * Boundary at which the padding values are added.
     * </p>
     *
     * @return static<TKey, TValue> A padded data structure.
     */
    public function pad (int $size, mixed $value, Side $side = Side::RIGHT):static;

}