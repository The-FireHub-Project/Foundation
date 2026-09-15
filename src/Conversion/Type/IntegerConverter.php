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

/**
 * ### Converts values into integers
 *
 * Provides the conversion rules required to interpret a value as an integer.
 *
 * The converter performs validation according to FireHub conversion rules and returns the resulting integer when the
 * conversion succeeds. If the value cannot be interpreted as a valid integer, the conversion fails.
 * @since 1.0.0
 */
final readonly class IntegerConverter extends Type {

    /**
     * {@inheritDoc}
     *
     * - Converts the value to an integer if it is an integer or a string that can be converted to an integer.
     * - Returns null if the conversion fails.
     *
     * <code>
     * use FireHub\Foundation\Conversion\Type\IntegerConverter;
     *
     * $array = new IntegerConverter('10')->convert();
     *
     * // 10
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::int() To check whether the value is an integer.
     * @uses \FireHub\Runtime\DataIs::string() To check whether the value is a string.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check whether the value is a string that can be converted to
     * an integer.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return null|int The converted value, or null if the conversion fails.
     */
    public function convert ():?int {

        return match (true) {
            Runtime\DataIs::int($this->value)
            || (
                Runtime\DataIs::string($this->value)
                && Runtime\Str\SB\Regex::match('/^-?\d+$/', $this->value)
            ) => (int)$this->value,
            default => null
        };

    }

}