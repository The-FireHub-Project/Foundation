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

namespace FireHub\Foundation\Number;

use FireHub\Core\Type\Number\Real as BaseReal;
use FireHub\Core\Meta\Enum\Number\Format;
use FireHub\Foundation\Conversion\Policy\Strict;
use FireHub\Foundation\Number\Operation\NumericFormat;
use FireHub\Foundation\Number\Exception\InvalidConversionException;
use FireHub\Runtime;

/**
 * ### Provides an immutable real number value object with a high-level developer API
 *
 * The Real class represents a real number value within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with floating-point values while preserving
 * immutable value semantics inherited from the Core Value Object system.
 *
 * The class implements the Core real number type contract and extends the base Number Value Object abstraction,
 * allowing floating-point values to be used consistently across the FireHub ecosystem.
 *
 * This class is responsible for high-level real number operations and developer experience, while low-level
 * floating-point execution remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of float
 *
 * @extends \FireHub\Core\Type\Number\Real<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Real extends BaseReal {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * The float value.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected float $value
    ) {}

    /**
     * ### Creates a new real instance from a given value
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = Real::of('1.1');
     *
     * // 1.1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Policy\Strict::float() To convert the value to a real.
     *
     * @param mixed $value <p>
     * The value to convert to a real.
     * </p>
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     *
     * @return static<float> A new Real instance representing the real value.
     */
    public static function of (mixed $value):static {

        return new static(new Strict($value)->float());

    }

    /**
     * ### Converts a formatted decimal string into a normalized decimal string
     *
     * Removes the thousand separators and converts the decimal separator to a dot.
     *
     * The returned value is suitable for use as a normalized decimal representation.
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = Real::fromFormat('1.234,56', ',', '.');
     *
     * // 1234.56
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Number\Operation\NumericFormat::toString() To convert the value to a normalized.
     *
     * @param string $number <p>
     * The formatted decimal number.
     * </p>
     * @param string $decimal_separator [optional] <p>
     * The decimal separator used by the input value.
     * </p>
     * @param string $thousands_separator [optional] <p>
     * The thousands separator used by the input value.
     * </p>
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException If the value is not a valid decimal
     * number.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return static<float> Normalized decimal string.
     */
    public static function fromFormat (string $number, string $decimal_separator = '.', string $thousands_separator = ','):static {

        return new static(
            (float)new NumericFormat(
                $number,
                $decimal_separator,
                $thousands_separator
            )->toString()
        );

    }

    /**
     * ### Creates a float value from a predefined numeric format
     *
     * Parses a formatted float representation using a predefined numeric formatting convention.
     *
     * <code>
     * use FireHub\Foundation\Float;
     * use FireHub\Core\Meta\Enum\Number\Format;
     *
     * $real = Float::fromStandard('1 234.56', Format::SI)->value();
     *
     * // 1234.56
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Number\Real::fromFormat() To parse the formatted decimal value.
     * @uses \FireHub\Core\Meta\Enum\Number\Format::decimalSeparator() To get the decimal separator.
     * @uses \FireHub\Core\Meta\Enum\Number\Format::thousandsSeparator() To get the thousands separator.
     *
     * @param string $value <p>
     * The formatted decimal value.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Number\Format $format <p>
     * The numeric formatting convention used by the value.
     * </p>
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException If the value is not a valid decimal
     * number.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return static<float> A new Real instance.
     */
    public static function fromStandard (string $value, Format $format):static {

        return self::fromFormat(
            $value,
            $format->decimalSeparator(),
            $format->thousandsSeparator()
        );

    }

    /**
     * ### Converts the real value to an integer number
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $integer = new Real(10.0)->toInteger();
     *
     * // 10
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::floor() To round the real value down to the nearest integer.
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidConversionException If the real value cannot be converted
     * to an integer without losing its fractional part.
     *
     * @return \FireHub\Foundation\Number\Integer<int> Returns a new instance of the Integer class.
     */
    public function toInteger ():Integer {

        if ($this->value != ($floor = Runtime\Math::floor($this->value))) // @phpstan-ignore notEqual.notAllowed
            throw new InvalidConversionException(
                'The real value cannot be converted to an integer without losing its fractional part.',
                [
                    'value' => $this->value,
                    'compare_with' => $floor,
                ]
            );

        return new Integer((int)$this->value);

    }

    /**
     * ### Converts the real value to a decimal number
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $decimal = new Real(10.2)->toDecimal();
     *
     * // '10.2'
     * </code>
     *
     * @since 1.0.0
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException If the value is not a valid decimal
     * number.
     *
     * @return \FireHub\Foundation\Number\Decimal<numeric-string> Returns a new instance of the Decimal class.
     */
    public function toDecimal ():Decimal {

        /** @var \FireHub\Foundation\Number\Decimal<numeric-string> */
        return new Decimal((string)$this->value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = new Real(9.99)->value();
     *
     * // 9.99
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():float {

        return $this->value;

    }

}