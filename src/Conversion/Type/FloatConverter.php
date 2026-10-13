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
 * ### Converts values into floating-point numbers
 *
 * Provides the conversion rules required to interpret a value as a floating-point number.
 *
 * The converter validates the supplied value and returns the corresponding floating-point representation when the
 * conversion succeeds. If the value cannot be interpreted as a valid floating-point number, the conversion fails.
 * @since 1.0.0
 */
final readonly class FloatConverter extends Type {

    /**
     * {@inheritDoc}
     *
     * - A numeric value is returned as is.
     * - A string value is converted to a floating-point number using the `floatval()` function.
     *
     * <code>
     * use FireHub\Foundation\Conversion\Type\FloatConverter;
     *
     * $array = new FloatConverter('123')->convert();
     *
     * // 123
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::numeric() To check whether the value is numeric.
     *
     * @return null|float Returns the converted value, or null if the conversion fails.
     */
    public function convert ():?float {

        return match (true) {
            Runtime\DataIs::numeric($this->value) => (float)$this->value,
            default => null
        };

    }

}