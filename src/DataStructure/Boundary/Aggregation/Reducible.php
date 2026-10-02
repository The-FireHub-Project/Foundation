<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Boundary\Aggregation;

/**
 * ### Defines the ability to reduce values into a single accumulated result
 *
 * Defines a data structure or storage implementation capable of reducing its values into a single accumulated
 * result by applying a callback to each value.
 * @since 1.0.0
 *
 * @template TValue
 */
interface Reducible {

    /**
     * ### Reduces values into a single accumulated result
     * @since 1.0.0
     *
     * @template TCarry
     *
     * @param TCarry $initial <p>
     * The initial accumulator value.
     * </p>
     * @param callable(TCarry, TValue):TCarry $callback <p>
     * The callback used to accumulate each value into the result.
     * </p>
     *
     * @return TCarry The accumulated result.
     */
    public function reduce (mixed $initial, callable $callback):mixed;

}