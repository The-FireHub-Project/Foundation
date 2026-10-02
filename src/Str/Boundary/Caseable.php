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

namespace FireHub\Foundation\Str\Boundary;

use FireHub\Foundation\Str\Case\Casing;

/**
 * ### Defines an object that supports string casing transformations
 *
 * Provides a contract for string-based value objects that can be transformed between different letter casing
 * formats, such as lowercase, uppercase, and swapped case representations.
 *
 * This interface allows casing operations to be applied consistently across different string value objects while
 * preserving their immutable behavior.
 * @since 1.0.0
 */
interface Caseable {

    /**
     * ### Returns the case converter object for this string
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Str\Case\Casing<$this> Returns a new instance of the Casing class.
     */
    public function case ():Casing;

}