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
 * ### Converts values into booleans
 *
 * Provides the conversion rules required to interpret a value as a boolean.
 *
 * The converter recognizes the boolean representations supported by the FireHub Foundation layer and returns the
 * corresponding boolean value when the conversion succeeds. Unsupported representations cause the conversion to fail.
 * @since 1.0.0
 */
final readonly class BooleanConverter extends Type {

    /**
     * {@inheritDoc}
     *
     * - A boolean value is returned as is.
     * - A string value is converted to a boolean using the following rules:
     *   - `'true'`, `'1'`, `'yes'`, `'on'` are converted to `true`.
     *   - `'false'`, `'0'`, `'no'`, `'off'` are converted to `false`.
     *   - All other strings are converted to `null`.
     *
     * <code>
     * use FireHub\Foundation\Conversion\Type\BooleanConverter;
     *
     * $array = new BooleanConverter('yes')->convert();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::bool() To check whether the value is already a boolean.
     * @uses \FireHub\Runtime\DataIs::string() To check whether the value is a string.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the string to lowercase.
     *
     * @return null|bool Returns the converted value, or null if the conversion fails.
     */
    public function convert ():?bool {

        if (Runtime\DataIs::bool($this->value)) return $this->value;

        $value = Runtime\DataIs::string($this->value)
            ? Runtime\Str\SB\Casing::toLower($this->value)
            : $this->value;

        return match ($value) {
            'true', '1', 'yes', 'on' => true,
            'false', '0', 'no', 'off' => false,
            default => null
        };

    }

}