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
 * ### Provides a hash strategy for string values
 *
 * String hash strategy provides deterministic hashing and equality comparison for string values used by
 * hash-based storage implementations.
 *
 * Values are considered equal when they contain exactly the same sequence of bytes. Equal values therefore always
 * produce the same hash, while hash collisions between different values are resolved through equality comparison.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<string>
 */
final readonly class StringHashStrategy implements Strategy {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::string() To check if the specified value is a string.
     * @uses \FireHub\Runtime\Data::getDebugType() To get the debug type of the specified value.
     * @uses \FireHub\Runtime\Hash::hash() To hash the specified value.
     * @uses \FireHub\Runtime\Hash\Algorithm::XXH3 To use the XXH3 algorithm for hashing.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashValueTypeException If the specified
     * value is not a string.
     */
    public function hash (mixed $value):string {

        if (!Runtime\DataIs::string($value))
            throw new InvalidHashValueTypeException(
                "Invalid hash value type.",
                [
                    'type' => Runtime\Data::getDebugType($value),
                ]
            );

        return Runtime\Hash::hash(
            Runtime\Hash\Algorithm::XXH3,
            $value
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::string() To check if both values are strings.
     */
    public function equals (mixed $left, mixed $right):bool {

        return Runtime\DataIs::string($left)
            && Runtime\DataIs::string($right)
            && $left === $right;

    }

}