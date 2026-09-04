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

namespace FireHub\Foundation\Conversion\Type;

use FireHub\Foundation\Conversion\Type;
use FireHub\Runtime;
use BackedEnum, Stringable, UnitEnum;

/**
 * ### Converts values into strings
 *
 * Provides the conversion rules required to interpret a value as a string.
 *
 * The converter transforms supported scalar and object values into their string representation, according to the FireHub
 * Foundation conversion rules. If the supplied value cannot be represented as a valid string, the conversion fails.
 * @since 1.0.0
 */
final readonly class StringConverter extends Type {

    /**
     * {@inheritDoc}
     *
     * - A string value is returned as is.
     * - A scalar value is converted to a string using the `strval()` function.
     * - A `Stringable` object is converted to a string using the `__toString()` method.
     * - A `BackedEnum` object is converted to a string using the `value` property.
     * - A `UnitEnum` object is converted to a string using the `name` property.
     *
     * <code>
     * use FireHub\Foundation\Conversion\Type\StringConverter;
     *
     * $array = new StringConverter(10)->convert();
     *
     * // '10'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::string() To check whether the value is already a string.
     * @uses \FireHub\Runtime\DataIs::scalar() To check whether the value is a scalar.
     *
     * @return null|string Returns the converted value, or null if the conversion fails.
     */
    public function convert ():?string {

        return match (true) {
            Runtime\DataIs::string($this->value) => $this->value,
            Runtime\DataIs::scalar($this->value) => (string)$this->value,
            $this->value instanceof Stringable => $this->value->__toString(),
            $this->value instanceof BackedEnum => (string)$this->value->value,
            $this->value instanceof UnitEnum => $this->value->name,
            default => null
        };

    }

}