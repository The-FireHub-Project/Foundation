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
use FireHub\Foundation\DataStructure\Storage\Hash\ValueEncoder;
use FireHub\Runtime;

/**
 * ### Provides a hash strategy for integer values
 *
 * Integer hash strategy provides deterministic hashing and strict equality comparison for integer values used by
 * hash-based storage implementations.
 *
 * Values are considered equal only when they are identical integers. Equal values always produce the same hash,
 * while hash collisions between different values are resolved through equality comparison.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<int>
 */
final readonly class IntHashStrategy implements Strategy {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Hash::hash() To hash the specified value.
     * @uses \FireHub\Runtime\Hash\Algorithm::XXH3 To use the XXH3 algorithm for hashing.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\ValueEncoder::encode() To encode the specified value.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashValueTypeException If the specified
     * value is not a valid data type.
     */
    public function hash (mixed $value):string {

        return Runtime\Hash::hash(
            Runtime\Hash\Algorithm::XXH3,
            new ValueEncoder()->encode($value)
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::int() To check if both values are integers.
     */
    public function equals (mixed $left, mixed $right):bool {

        return Runtime\DataIs::int($left)
            && Runtime\DataIs::int($right)
            && $left === $right;

    }

}