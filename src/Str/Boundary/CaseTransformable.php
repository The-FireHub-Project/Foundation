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

use FireHub\Foundation\Str\Case\Converter;

/**
 * ### Defines the contract for strings supporting case transformations
 *
 * Provides a capability contract for string value objects that support conversion between different casing formats.
 * @since 1.0.0
 */
interface CaseTransformable extends Caseable {

    /**
     * ### Transform the string to a different case format
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Str\Case\Converter<$this> Returns a new instance of the Converter class.
     */
    public function transform ():Converter;

}