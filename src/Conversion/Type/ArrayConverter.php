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
use JsonSerializable, Traversable;

/**
 * ### Converts values into arrays
 *
 * Provides the conversion rules required to interpret a value as an array.
 *
 * Depending on the supplied value and conversion options, the converter produces an array representation or reports a
 * conversion failure when no valid conversion is possible.
 * @since 1.0.0
 */
final readonly class ArrayConverter extends Type {

    /**
     * {@inheritDoc}
     *
     * - An array is returned as is.
     * - A traversable value is converted to an array using the `Iterator::toArray()` method.
     * - A `JsonSerializable` object is converted to an array using the `jsonSerialize()` method.
     *
     * <code>
     * use FireHub\Foundation\Conversion\Type\ArrayConverter;
     *
     * $array = new ArrayConverter([1, 2, 3])->convert();
     *
     * // [1, 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::array() To check whether the value is already an array.
     * @uses \FireHub\Runtime\Iterator::toArray() To convert a traversable value to an array.
     *
     * @return null|array<array-key, mixed> The converted value, or null if the conversion fails.
     */
    public function convert ():?array {

        return match (true) {
            Runtime\DataIs::array($this->value) => $this->value,
            $this->value instanceof Traversable => Runtime\Iterator::toArray($this->value),
            $this->value instanceof JsonSerializable => (array)$this->value->jsonSerialize(),
            default => null
        };

    }

}