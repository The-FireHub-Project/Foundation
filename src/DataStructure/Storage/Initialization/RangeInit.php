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

namespace FireHub\Foundation\DataStructure\Storage\Initialization;

use FireHub\Foundation\DataStructure\Storage\Initializer;
use Generator;

/**
 * ### Provides an initialization strategy based on a range of values
 *
 * Range initializer generates a sequential series of values from a defined starting point, ending point,
 * and optional step. The generated values are supplied as the initial contents of a storage while preserving
 * their generated order.
 *
 * The initializer defines how the initial values are generated but does not determine how those values are
 * represented, stored, accessed, or managed by the consuming storage.
 * @since 1.0.0
 *
 * @template TKey of int
 * @template TValue of int|float
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Initializer<TKey, TValue>
 */
final readonly class RangeInit implements Initializer {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $start <p>
     * First value of the sequence.
     * </p>
     * @param TValue $end <p>
     * The sequence is ended upon reaching the end value.
     * </p>
     * @param TValue $step [optional] <p>
     * If a step value is given, it will be used as the increment between elements in the sequence.
     *
     * Step should be given as a positive number. If not specified, a step will default to 1.
     * </p>
     *
     * @return void
     */
    public function __construct(
        private int|float $start,
        private int|float $end,
        private int|float $step = 1
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return Generator<TKey, TValue> The generator of values to use as the initial data for the storage.
     */
    public function initialize ():Generator {

        $key = 0; $value = $this->start;
        while ($value <= $this->end) {

            /**
             * @var TKey $key
             * @var TValue $value
             */
            yield $key++ => $value;

            $value += $this->step;

        }

    }

}