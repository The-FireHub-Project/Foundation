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

use FireHub\Core\Type\Number;
use FireHub\Core\Type\Number\ {
    Integer as BaseInteger, Real as BaseReal
};
use FireHub\Core\Meta\Enum\Number\Format;
use FireHub\Foundation\Conversion\Policy\Strict;
use FireHub\Foundation\Number\Operation\NumericFormat;
use FireHub\Foundation\Number\Exception\InvalidConversionException;
use FireHub\Runtime\Math\RoundMode;
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
     * ### Converts the real value into a formatted decimal string
     *
     * Formats the real value using the given decimal and the thousand separators.
     *
     * <code>
     * use FireHub\Foundation\Number\Real;
     *
     * $real = new Real(1234.56);
     *
     * $real->toFormat(',', '.');
     *
     * // '1.234,56'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the real value into integer and fractional parts.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join the integer and fractional parts into a string.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To access parts of the string.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     * @uses \FireHub\Runtime\Arr\Transform::reverse() To reverse the array.
     *
     * @param string $decimal_separator [optional] <p>
     * The decimal separator to use.
     * </p>
     * @param string $thousands_separator [optional] <p>
     * The thousands separator to use.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\EmptySeparatorException If the separator is an empty string.
     *
     * @return string The formatted real value.
     */
    public function toFormat (string $decimal_separator = '.', string $thousands_separator = ','):string {

        $parts = Runtime\Str\SB\Delimiter::explode((string)$this->value, '.', 2);

        $integer = $parts[0] ?? '';
        $fraction = $parts[1] ?? '';

        $sign = '';

        if ($integer !== '' && ($integer[0] === '-' || $integer[0] === '+')) {

            $sign = $integer[0];
            $integer = Runtime\Str\SB\Access::part($integer, 1);

        }

        $groups = [];

        while (Runtime\Str\SB\Inspection::length($integer) > 3) {

            $groups[] = Runtime\Str\SB\Access::part($integer, -3);
            $integer = Runtime\Str\SB\Access::part($integer, 0, -3);

        }

        $groups[] = $integer;

        $integer = Runtime\Str\SB\Delimiter::implode(
            Runtime\Arr\Transform::reverse($groups),
            $thousands_separator
        );

        return $sign.$integer.(
            $fraction !== ''
                ? $decimal_separator.$fraction
                : ''
            );

    }

    /**
     * ### Converts the real value into a predefined numeric format
     *
     * Formats the real value according to a predefined numeric formatting convention.
     *
     * <code>
     * use FireHub\Foundation\Number\Real;
     * use FireHub\Core\Meta\Enum\Number\Format;
     *
     * $real = new Real(1234.56);
     *
     * $real->toStandard(Format::SI);
     *
     * // '1 234.56'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Number\Real::toFormat() To format the real value.
     * @uses \FireHub\Core\Meta\Enum\Number\Format::decimalSeparator() To get the decimal separator.
     * @uses \FireHub\Core\Meta\Enum\Number\Format::thousandsSeparator() To get the thousands separator.
     *
     * @param \FireHub\Core\Meta\Enum\Number\Format $format <p>
     * The numeric formatting convention used by the value.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\EmptySeparatorException If the separator is an empty string.
     *
     * @return string The formatted real value.
     */
    public function toStandard (Format $format):string {

        return $this->toFormat(
            $format->decimalSeparator(),
            $format->thousandsSeparator()
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = new Real(10.0)->isFinite();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::isFinite() To check if the real value is finite.
     */
    public function isFinite ():bool {

        return Runtime\Math::isFinite($this->value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Rea;
     *
     * $real = new Real(10.0)->isInfinite();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::isInfinite() To check if the real value is infinite.
     */
    public function isInfinite ():bool {

        return Runtime\Math::isInfinite($this->value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Rea;
     *
     * $real = new Real(10.0)->isNaN();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::isNaN() To check if the real value is NaN.
     */
    public function isNaN ():bool {

        return Runtime\Math::isNaN($this->value);

    }

    /**
     * ### Converts the real value to an integer number
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = new Real(10.0)->toInteger();
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

    /**
     * ### Adds a value to the current real value
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = new Real(10.2)->add(2.0);
     *
     * // 12.2
     * </code>
     *
     * @since 1.0.0
     *
     * @param float|self<float> $value <p>
     * The value to add to the current value.
     * </p>
     *
     * @return static<float> Returns a new instance of the Real class with the sum of the current value and the given
     * value.
     */
    public function add (float|self $value):static {

        return new static(
            $this->value + ($value instanceof self ? $value->value() : $value)
        );

    }

    /**
     * ### Subtracts a value to the current real value
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = new Real(10.2)->subtract(2.0);
     *
     * // 8.2
     * </code>
     *
     * @since 1.0.0
     *
     * @param float|self<float> $value <p>
     * The value to subtract to the current value.
     * </p>
     *
     * @return static<float> Returns a new instance of the Real class with the difference of the current value and the
     * given value.
     */
    public function subtract (float|self $value):static {

        return new static(
            $this->value - ($value instanceof self ? $value->value() : $value)
        );

    }

    /**
     * ### Multiplies the current real value by a value
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = new Real(10.5)->multiply(2.0);
     *
     * // 21.0
     * </code>
     *
     * @since 1.0.0
     *
     * @param float|self<float> $value <p>
     * The value to multiply the current value by.
     * </p>
     *
     * @return static<float> Returns a new instance of the Real class with the product of the current value and the
     * given value.
     */
    public function multiply (float|self $value):static {

        return new static(
            $this->value * ($value instanceof self ? $value->value() : $value)
        );

    }

    /**
     * ### Divides the current real value by a value
     *
     * <code>
     * use FireHub\Foundation\Real;
     *
     * $real = new Real(10.0)->divide(4.0);
     *
     * // 2.5
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::divideFloat() To divide the current value by the given value.
     *
     * @param float|self<float> $value <p>
     * The value to divide the current value by.
     * </p>
     *
     * @return static<float> Returns a new instance of the Real class with the quotient of the current value and the
     * given value.
     */
    public function divide (float|self $value):static {

        return new static(
            Runtime\Math::divideFloat($this->value, ($value instanceof self ? $value->value() : $value))
        );

    }

    /**
     * ### Raises the current real value to a power
     *
     * <code>
     * use FireHub\Foundation\Number\Real;
     *
     * $real = new Real(2.5)->power(2);
     *
     * // 6.25
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::pow() To raise the current value to the given exponent.
     *
     * @param float|int|self<float> $exponent <p>
     * The exponent to raise the current value to.
     * </p>
     *
     * @return static<float> Returns a new Real instance with the result.
     */
    public function power (float|int|self $exponent):static {

        return new static(
            Runtime\Math::pow($this->value, $exponent instanceof self ? $exponent->value() : $exponent)
        );

    }

    /**
     * ### Calculates the floating-point remainder of the division
     *
     * Returns the floating-point remainder of dividing the current real value by the given value.
     *
     * <code>
     * use FireHub\Foundation\Number\Real;
     *
     * $real = new Real(10.5)->remainder(3.0);
     *
     * // 1.5
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::remainder() To calculate the floating-point remainder.
     *
     * @param float|int|self<float> $value <p>
     * The divisor.
     * </p>
     *
     * @return static<float> Returns a new Real instance containing the remainder.
     */
    public function remainder (float|int|self $value):static {

        return new static(
            Runtime\Math::remainder($this->value, ($value instanceof self ? $value->value() : $value))
        );

    }

    /**
     * ### Returns the absolute value
     *
     * Returns a new Real instance containing the absolute value of the current real number.
     *
     * <code>
     * use FireHub\Foundation\Number\Real;
     *
     * $real = new Real(-10.5)->absolute();
     *
     * // 10.5
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::abs() To calculate the absolute value.
     *
     * @return static<float> Returns a new Real instance containing the absolute value.
     */
    public function absolute ():static {

        return new static(
            Runtime\Math::abs($this->value)
        );

    }

    /**
     * ### Rounds the real value
     *
     * Returns a new Real instance containing the rounded value.
     *
     * <code>
     * use FireHub\Foundation\Number\Real;
     * use FireHub\Runtime\Math\RoundMode;
     *
     * $real = new Real(10.567)->round(2);
     *
     * // 10.57
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::round() To round the value.
     *
     * @param int $precision [optional] <p>
     * The number of decimal digits to round to.
     * </p>
     * @param \FireHub\Runtime\Math\RoundMode $mode [optional]
     * The rounding mode.
     *
     * @return static<float> Returns a new Real instance containing the rounded value.
     */
    public function round (int $precision = 0, RoundMode $mode = RoundMode::HALF_AWAY_FROM_ZERO):static {

        return new static(
            (float)Runtime\Math::round(
                $this->value,
                $precision,
                $mode
            )
        );

    }

    /**
     * ### Rounds the real value up
     *
     * Returns the smallest integer greater than or equal to the current real value.
     *
     * <code>
     * use FireHub\Foundation\Number\Real;
     *
     * $integer = new Real(10.25)->ceil();
     *
     * // 11
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::ceil() To round the value up.
     *
     * @return \FireHub\Foundation\Number\Integer<int> Returns a new Integer instance containing the rounded value.
     */
    public function ceil ():BaseInteger {

        return new Integer(
            Runtime\Math::ceil($this->value)
        );

    }

    /**
     * ### Rounds the real value down
     *
     * Returns the largest integer less than or equal to the current real value.
     *
     * <code>
     * use FireHub\Foundation\Number\Real;
     *
     * $integer = new Real(10.75)->floor();
     *
     * // 10
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::floor() To round the value down.
     *
     * @return \FireHub\Foundation\Number\Integer<int> Returns a new Integer instance containing the rounded value.
     */
    public function floor ():BaseInteger {

        return new Integer(
            Runtime\Math::floor($this->value)
        );

    }

}