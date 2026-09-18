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
 * ### Provides a hash strategy for floating-point values
 *
 * Float hash strategy provides deterministic hashing and strict equality comparison for floating-point values
 * used by hash-based storage implementations.
 *
 * Values are considered equal according to strict floating-point equality. Equal values always produce the same
 * hash, while hash collisions between different values are resolved through equality comparison.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<float>
 */
final readonly class FloatHashStrategy implements Strategy {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::float() To check if the specified value is a float.
     * @uses \FireHub\Runtime\Data::getDebugType() To get the debug type of the specified value.
     * @uses \FireHub\Runtime\Hash::hash() To hash the specified value.
     * @uses \FireHub\Runtime\Hash\Algorithm::XXH3 To use the XXH3 algorithm for hashing.
     * @uses \FireHub\Runtime\Binary::pack() To pack the specified floating-point number into a binary string.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashValueTypeException If the specified
     * value is not a float.
     */
    public function hash (mixed $value):string {

        if (!Runtime\DataIs::float($value))
            throw new InvalidHashValueTypeException(
                "Invalid hash value type.",
                [
                    'type' => Runtime\Data::getDebugType($value),
                ]
            );

        if ($value === 0.0) $value = 0.0;

        return Runtime\Hash::hash(
            Runtime\Hash\Algorithm::XXH3,
            Runtime\Binary::pack('E', $value)
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::float() To check if both values are floats.
     */
    public function equals (mixed $left, mixed $right):bool {

        return Runtime\DataIs::float($left)
            && Runtime\DataIs::float($right)
            && $left === $right;

    }

}