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
 * ### Provides a hash strategy for mixed scalar values
 *
 * Mixed hash strategy provides deterministic hashing and strict equality comparison for null, boolean, integer,
 * floating-point, string values, and objects used by hash-based storage implementations.
 *
 * The value type forms part of the hash representation, ensuring that values of different types remain logically
 * distinct even when their textual representations are identical.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<mixed>
 */
final readonly class MixedHashStrategy implements Strategy {

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
     */
    public function equals (mixed $left, mixed $right):bool {

        return $left === $right;

    }

}