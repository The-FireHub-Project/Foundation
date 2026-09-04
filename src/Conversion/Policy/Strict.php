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
use FireHub\Foundation\Conversion\Exception\ConversionException;

/**
 * ### Performs strict value conversions
 *
 * Provides a strict conversion API for transforming values into supported native types.
 *
 * The Strict conversion policy delegates the actual conversion to dedicated converter classes and guarantees that the
 * requested conversion succeeds. If a conversion cannot be performed, an appropriate exception is thrown.
 *
 * This API is intended for scenarios where conversion failures are considered exceptional and should immediately
 * stop execution.
 * @since 1.0.0
 */
final readonly class Strict {

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
     * ### Converts the value to the array
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\ArrayConverter::convert() To convert the value to an array.
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     *
     * @return array<array-key, mixed> The converted value.
     */
    public function array ():array {

        return new ArrayConverter($this->value)->convert()
            ?? throw new ConversionException(
                'Unable to convert value the array',
                [
                    'value' => $this->value,
                    'type' => ArrayConverter::class,
                ]
            );

    }

    /**
     * ### Converts the value to the boolean
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\BooleanConverter::convert() To convert the value to an boolean.
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     *
     * @return bool The converted value.
     */
    public function bool ():bool {

        return new BooleanConverter($this->value)->convert()
            ?? throw new ConversionException(
                'Unable to convert value the boolean',
                [
                    'value' => $this->value,
                    'type' => BooleanConverter::class,
                ]
            );

    }

    /**
     * ### Converts the value to the float
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\FloatConverter::convert() To convert the value to an float.
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     *
     * @return float The converted value.
     */
    public function float ():float {

        return new FloatConverter($this->value)->convert()
            ?? throw new ConversionException(
                'Unable to convert value the float',
                [
                    'value' => $this->value,
                    'type' => FloatConverter::class,
                ]
            );

    }

    /**
     * ### Converts the value to an integer
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\IntegerConverter::convert() To convert the value to an integer.
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return int The converted value.
     */
    public function int ():int {

        return new IntegerConverter($this->value)->convert()
            ?? throw new ConversionException(
                'Unable to convert value to integer',
                [
                    'value' => $this->value,
                    'type' => IntegerConverter::class,
                ]
            );

    }

    /**
     * ### Converts the value to the string
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Type\StringConverter::convert() To convert the value to an string.
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     *
     * @return string The converted value.
     */
    public function string ():string {

        return new StringConverter($this->value)->convert()
            ?? throw new ConversionException(
                'Unable to convert value the string',
                [
                    'value' => $this->value,
                    'type' => StringConverter::class,
                ]
            );

    }

}