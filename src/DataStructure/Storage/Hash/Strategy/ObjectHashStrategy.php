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
 * ### Provides a hash strategy for object values
 *
 * Object hash strategy provides identity-based hashing and equality comparison for object values used by hash-based
 * storage implementations.
 *
 * Objects are considered equal only when they reference the same object instance. Equal objects therefore always
 * produce the same hash, while hash collisions between different objects are resolved through identity comparison.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<object>
 */
final readonly class ObjectHashStrategy implements Strategy {

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
     * @uses \FireHub\Runtime\DataIs::object() To check if both values are objects.
     */
    public function equals (mixed $left, mixed $right):bool {

        return Runtime\DataIs::object($left)
            && Runtime\DataIs::object($right)
            && $left === $right;

    }

}