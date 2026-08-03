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
use FireHub\Core\Type\Str\Encoding;

/**
 * ### Defines the contract for strings supporting case transformations
 *
 * Provides a capability contract for string value objects that support conversion between different casing formats.
 * @since 1.0.0
 */
interface CaseTransformable {

    /**
     * ### Creates a new instance of the string value object with a different case format
     * @since 1.0.0
     *
     * @param string $value <p>
     * The string value to convert.
     * </p>
     * @param \FireHub\Core\Type\Str\Encoding $encoding <p>
     * The encoding of the string.
     * </p>
     *
     * @return static A new instance of the string value object with the converted case.
     */
    public static function of (string $value, Encoding $encoding):static;

    /**
     * ### Returns the string value
     * @since 1.0.0
     *
     * @return string The string value.
     */
    public function value ():string;

    /**
     * ### Returns the encoding of the string
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\Str\Encoding The encoding of the string.
     */
    public function encoding ():Encoding;

    /**
     * ### Converts the string to a different case format
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Str\Case\Converter<$this> Returns a new instance of the CaseConverter class.
     */
    public function toCase ():Converter;

}