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

namespace FireHub\Foundation\DataStructure\Storage\Hash;

use FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashValueTypeException;
use FireHub\Runtime;

/**
 * ### Encodes mixed values for deterministic hashing
 *
 * Converts supported PHP values into type-aware and unambiguous representations suitable for deterministic hashing.
 *
 * Arrays are encoded recursively while preserving key types, value types, element order, and nested structure.
 * Objects and resources are represented according to their runtime identity.
 * @since 1.0.0
 */
final readonly class ValueEncoder {

    /**
     * ### Encodes a value
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::bool() To check if the specified value is a boolean.
     * @uses \FireHub\Runtime\DataIs::int() To check if the specified value is an integer.
     * @uses \FireHub\Runtime\DataIs::float() To check if the specified value is a floating-point number
     * @uses \FireHub\Runtime\DataIs::string() To check if the specified value is a string.
     * @uses \FireHub\Runtime\DataIs::object() To check if the specified value is an object.
     * @uses \FireHub\Runtime\DataIs::resource() To check if the specified value is a resource.
     * @uses \FireHub\Runtime\DataIs::array() To check if the specified value is an array.
     * @uses \FireHub\Runtime\Binary::pack() To pack the specified floating-point number into a binary string.
     * @uses \FireHub\Runtime\ResourceManager::id() To get the identity of the specified resource.
     * @uses \FireHub\Runtime\ObjectModel\Identity::id() To get the identity of the specified object.
     * @uses \FireHub\Runtime\Data::getDebugType() To get the debug type of the specified value.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\ValueEncoder::encodeArray() To encode an array.
     *
     * @param mixed $value <p>
     * Value to encode.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashValueTypeException If the specified
     * value cannot be encoded.
     *
     * @return non-empty-string The encoded representation.
     */
    public function encode (mixed $value):string {

        return match (true) {
            $value === null => 'null:',
            Runtime\DataIs::bool($value) =>
                'bool:'.($value ? '1' : '0'),
            Runtime\DataIs::int($value) =>
                "int:$value",
            Runtime\DataIs::float($value) =>
                'float:'.Runtime\Binary::pack('E', $value === 0.0 ? 0.0 : $value),
            Runtime\DataIs::string($value) =>
                'string:'.$value,
            Runtime\DataIs::array($value) =>
                $this->encodeArray($value),
            Runtime\DataIs::object($value) =>
                'object:'.$value::class.':'.Runtime\ObjectModel\Identity::id($value),
            Runtime\DataIs::resource($value) =>
                'resource:'.Runtime\ResourceManager::id($value),
            default => throw new InvalidHashValueTypeException(
                "Invalid hash value type.",
                [
                    'type' => Runtime\Data::getDebugType($value),
                ]
            )
        };

    }

    /**
     * ### Encodes an array
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\ValueEncoder::segment() To create a length-prefixed segment.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\ValueEncoder::encode() To encode an array element.
     *
     * @param array<array-key, mixed> $value <p>
     * Array to encode.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashValueTypeException If the specified
     * value cannot be encoded.
     *
     * @return non-empty-string The encoded array representation.
     */
    private function encodeArray (array $value):string {

        $data = 'array:'.count($value).':';

        foreach ($value as $key => $item) {
            $data .= $this->segment($this->encode($key));
            $data .= $this->segment($this->encode($item));
        }

        return $data;

    }

    /**
     * ### Creates a length-prefixed segment
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the specified string.
     *
     * @param string $value <p>
     * Value to segment.
     * </p>
     *
     * @return non-empty-string The length-prefixed segment.
     */
    private function segment (string $value):string {

        return Runtime\Str\SB\Inspection::length($value).':'.$value;

    }

}