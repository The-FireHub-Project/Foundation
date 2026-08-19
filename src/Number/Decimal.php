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

use FireHub\Core\Type\Number\Decimal as BaseDecimal;
use FireHub\Core\Meta\Enum\Number\Format;
use FireHub\Foundation\Number\Operation\NumericFormat;
use FireHub\Foundation\Number\Exception\ {
    InvalidConversionException, InvalidFractionalException
};
use FireHub\Runtime;

/**
 * ### Provides an immutable decimal value object with a high-level developer API
 *
 * The Decimal class represents a decimal numeric value within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with precise decimal values while preserving
 * immutable value semantics inherited from the Core Value Object system.
 *
 * The class implements the Core numeric type contract and extends the base Number Value Object abstraction, allowing
 * decimal values to be used consistently across the FireHub ecosystem.
 *
 * This class is responsible for high-level decimal operations and developer experience, while low-level numeric
 * execution remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of numeric-string
 *
 * @extends \FireHub\Core\Type\Number\Decimal<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Decimal extends BaseDecimal {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * The float value.
     * </p>
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check whether the value is a valid decimal number.
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException If the value is not a valid decimal
     * number.
     *
     * @return void
     */
    public function __construct (
        protected string $value
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match('/^[+-]?(?:\d+(?:\.\d+)?|\.\d+)$/', $value) === true,
            fn() => new InvalidFractionalException('Decimal value must be a valid decimal number.')
        );

    }

    /**
     * ### Converts a formatted decimal string into a normalized decimal string
     *
     * Removes the thousand separators and converts the decimal separator to a dot.
     *
     * The returned value is suitable for use as a normalized decimal representation.
     *
     * <code>
     * use FireHub\Foundation\Decimal;
     *
     * $decimal = Decimal::fromFormat('1.234,56', ',', '.');
     *
     * // '1234.56'
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
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException If the value is not a valid decimal
     * number.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return static<numeric-string> Normalized decimal string.
     */
    public static function fromFormat (string $number, string $decimal_separator = '.', string $thousands_separator = ','):static {

        return new static(
            new NumericFormat(
                $number,
                $decimal_separator,
                $thousands_separator
            )->toString()
        );

    }

    /**
     * ### Creates a decimal value from a predefined numeric format
     *
     * Parses a formatted decimal representation using a predefined numeric formatting convention.
     *
     * <code>
     * use FireHub\Foundation\Decimal;
     * use FireHub\Core\Meta\Enum\Number\Format;
     *
     * $decimal = Decimal::fromStandard('1 234.56', Format::SI)->value();
     *
     * // 1234.56
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Number\Decimal::fromFormat() To parse the formatted decimal value.
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
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException If the value is not a valid decimal
     * number.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return static<numeric-string> A new Decimal instance.
     */
    public static function fromStandard (string $value, Format $format):static {

        return self::fromFormat(
            $value,
            $format->decimalSeparator(),
            $format->thousandsSeparator()
        );

    }

    /**
     * ### Converts the decimal value to an integer number
     *
     * <code>
     * use FireHub\Foundation\Decimal;
     *
     * $integer = new Decimal('10.0')->toInteger();
     *
     * // 10
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check whether the value is a valid integer.
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidConversionException If the real value cannot be converted
     * to an integer without losing its fractional part.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return \FireHub\Foundation\Number\Integer<int> Returns a new instance of the Integer class.
     */
    public function toInteger ():Integer {

        if (Runtime\Str\SB\Regex::match('/^[+-]?\d+\.?0*$/', $this->value) !== true)
            throw new InvalidConversionException(
                'The decimal value cannot be converted to an integer without losing its fractional part.'
            );

        return new Integer((int)$this->value);

    }

    /**
     * ### Converts the decimal value to a real number
     *
     * <code>
     * use FireHub\Foundation\Decimal;
     *
     * $real = new Decimal('10.2')->toReal();
     *
     * // 10.2
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Number\Real<float> Returns a new instance of the Real class.
     */
    public function toReal ():Real {

        return new Real((float)$this->value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Decimal;
     *
     * $decimal = new Decimal('9.99')->value();
     *
     * // '9.99'
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

}