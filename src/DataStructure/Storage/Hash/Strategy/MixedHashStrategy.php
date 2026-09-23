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

namespace FireHub\Foundation\DataStructure\Storage\Hash\Strategy;

use FireHub\Foundation\DataStructure\Storage\Hash\Strategy;
use FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashValueTypeException;
use FireHub\Runtime;

/**
 * ### Provides a hash strategy for mixed scalar values
 *
 * Mixed hash strategy provides deterministic hashing and strict equality comparison for null, boolean, integer,
 * floating-point, string values, and objects used by hash-based storage implementations.
 *
 * The value type forms part of the hash representation, ensuring that values of different types remain logically
 * distinct even when their textual representations are identical.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<null|bool|int|float|string|object>
 */
final readonly class MixedHashStrategy implements Strategy {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::bool() To check if the specified value is a boolean.
     * @uses \FireHub\Runtime\DataIs::int() To check if the specified value is an integer.
     * @uses \FireHub\Runtime\DataIs::float() To check if the specified value is a floating-point number
     * @uses \FireHub\Runtime\DataIs::string() To check if the specified value is a string.
     * @uses \FireHub\Runtime\DataIs::object() To check if the specified value is an object.
     * @uses \FireHub\Runtime\ObjectModel\Identity::id() To get the identity of the specified object.
     * @uses \FireHub\Runtime\Data::getDebugType() To get the debug type of the specified value.
     * @uses \FireHub\Runtime\Hash::hash() To hash the specified value.
     * @uses \FireHub\Runtime\Hash\Algorithm::XXH3 To use the XXH3 algorithm for hashing.
     * @uses \FireHub\Runtime\Binary::pack() To pack the specified floating-point number into a binary string.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashValueTypeException If the specified
     * value is not a valid data type.
     */
    public function hash (mixed $value):string {

        $data = match (true) {
            $value === null => "null:",
            Runtime\DataIs::bool($value) => "bool:" . ($value ? '1' : '0'),
            Runtime\DataIs::int($value) => "int:$value",
            Runtime\DataIs::float($value) => 'float:' . Runtime\Binary::pack('E', $value === 0.0 ? 0.0 : $value),
            Runtime\DataIs::string($value) => "string:$value",
            Runtime\DataIs::object($value) => "object:".$value::class.':'.Runtime\ObjectModel\Identity::id($value),
            default => throw new InvalidHashValueTypeException(
                "Invalid hash value type.",
                [
                    'type' => Runtime\Data::getDebugType($value),
                ]
            )
        };

        return Runtime\Hash::hash(
            Runtime\Hash\Algorithm::XXH3,
            $data
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function equals (mixed $left, mixed $right):bool {

        return $left === $right;

    }

}