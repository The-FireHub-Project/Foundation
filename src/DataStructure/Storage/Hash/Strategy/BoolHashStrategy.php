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
 * ### Provides a hash strategy for boolean values
 *
 * Boolean hash strategy provides deterministic hashing and strict equality comparison for boolean values used by
 * hash-based storage implementations.
 *
 * Values are considered equal only when they represent the same boolean state.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<bool>
 */
final readonly class BoolHashStrategy implements Strategy{

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
     * @uses \FireHub\Runtime\DataIs::bool() To check if both values are booleans.
     */
    public function equals (mixed $left, mixed $right):bool {

        return Runtime\DataIs::bool($left)
            && Runtime\DataIs::bool($right)
            && $left === $right;

    }

}