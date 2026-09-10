<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Foundation
 */

namespace FireHub\Foundation\Conversion\Policy;

use FireHub\Foundation\Conversion\Type\ {
    ArrayConverter, BooleanConverter, FloatConverter, IntegerConverter, StringConverter
};

/**
 * ### Performs safe value conversions
 *
 * Provides a safe conversion API for transforming values into supported native types.
 *
 * The Safe conversion policy delegates the actual conversion to dedicated converter classes and returns null
 * whenever a conversion cannot be completed instead of throwing an exception.
 *
 * This API is intended for scenarios where conversion failures are expected and should be handled through
 * conditional logic rather than exceptions.
 * @since 1.0.0
 */
final readonly class Safe {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param mixed $value <p>
     * The value to convert.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private mixed $value
    ) {}

    /**
     * ### Converts the value to the array representation
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\ArrayConverter::convert() To convert the value to an array.
     *
     * @return null|array<array-key, mixed> The converted value, or null if the conversion fails.
     */
    public function array ():?array {

        return new ArrayConverter($this->value)->convert();

    }

    /**
     * ### Converts the value to the boolian representation
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\BooleanConverter::convert() To convert the value to the boolian.
     *
     * @return null|bool The converted value, or null if the conversion fails.
     */
    public function bool ():?bool {

        return new BooleanConverter($this->value)->convert();

    }

    /**
     * ### Converts the value to the float representation
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\FloatConverter::convert() To convert the value to the float.
     *
     * @return null|float The converted value, or null if the conversion fails.
     */
    public function float ():?float {

        return new FloatConverter($this->value)->convert();

    }

    /**
     * ### Converts the value to the integer representation
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\IntegerConverter::convert() To convert the value to an integer.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return null|int The converted value, or null if the conversion fails.
     */
    public function int ():?int {

        return new IntegerConverter($this->value)->convert();

    }

    /**
     * ### Converts the value to the string representation
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\StringConverter::convert() To convert the value to the string.
     *
     * @return null|string The converted value, or null if the conversion fails.
     */
    public function string ():?string {

        return new StringConverter($this->value)->convert();

    }

}