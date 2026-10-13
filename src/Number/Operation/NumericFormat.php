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

namespace FireHub\Foundation\Number\Operation;

use FireHub\Foundation\Number\Exception\InvalidFractionalException;
use FireHub\Runtime;

/**
 * ### Provides formatting and parsing operations for numeric values
 *
 * The NumericFormat class provides shared functionality for converting numeric values between their native
 * representations and formatted string representations.
 *
 * It supports parsing formatted numeric strings using configurable decimal and a thousand separators, as well as
 * formatting numeric values for human-readable output.
 *
 * The class is designed to provide common formatting behavior for numeric value objects such as Decimal and Real,
 * while leaving numeric value semantics and arithmetic operations to the respective Value Objects.
 *
 * Low-level string manipulation remains delegated to the Runtime layer.
 * @since 1.0.0
 */
final readonly class NumericFormat {

    /**
     * ### Constructor
     * @since 1.0.0
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
     * @return void
     */
    public function __construct (
        private string $number,
        private string $decimal_separator = '.',
        private string $thousands_separator = ','
    ) {}

    /**
     * ### Converts a formatted decimal string into a normalized decimal string
     *
     * Removes the thousand separators and converts the decimal separator to a dot.
     *
     * The returned value is suitable for use as a normalized decimal representation.
     *
     * <code>
     * use FireHub\Foundation\Operation\NumericFormat;
     *
     * $format = new NumericFormat('1.234,56', ',', '.')->toString();
     *
     * // '1234.56'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check whether the value is a valid decimal number.
     * @uses \FireHub\Runtime\Str\SB\Regex::quote() To escape special characters in the regular expression.
     * @uses \FireHub\Runtime\Str\SB\Replace::replace() To remove the thousand separators and decimal separator to a
     * dot.
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException If the value is not a valid decimal
     * number.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return numeric-string Normalized decimal string.
     */
    public function toString ():string {

        if ($this->decimal_separator === $this->thousands_separator)
            throw new InvalidFractionalException(
                'The decimal separator and thousands separator must be different.'
            );

        $pattern = '/^[+-]?(?:\d+|' . Runtime\Str\SB\Regex::quote($this->thousands_separator, '/') . ')+'
            . '(?:' . Runtime\Str\SB\Regex::quote($this->decimal_separator, '/') . '\d+)?$/';

        if (Runtime\Str\SB\Regex::match($pattern, $this->number) !== true)
            throw new InvalidFractionalException(
                'The value is not a valid decimal number for the specified format.'
            );

        $number = Runtime\Str\SB\Replace::replace($this->thousands_separator, '', $this->number);

        /** @var numeric-string */
        return Runtime\Str\SB\Replace::replace($this->decimal_separator, '.', $number);

    }

}