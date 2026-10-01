<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Factory;

use FireHub\Foundation\DataStructure\Tuple;
use FireHub\Foundation\DataStructure\Storage\FixedStorage;
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    ArrayCallbackInit, ArrayInit, EmptyInit, FillInit, GeneratorCallbackInit, RangeInit
};
use FireHub\Runtime;
use Closure, Generator;

/**
 * ### Provides factory methods for creating tuple data structures
 *
 * Provides convenient factory methods for creating tuples backed by fixed-size storage.
 *
 * The storage capacity is determined when the tuple is created and remains fixed for the lifetime of the
 * underlying storage.
 * @since 1.0.0
 */
readonly class TupleFactory {

    /**
     * ### Creates a tuple from an array
     *
     * Creates a new tuple containing the values provided by the specified array.
     *
     * The storage capacity is automatically determined from the number of provided values.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit To initialize the storage with the
     * provided values.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage To provide fixed-size storage.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of values in the array.
     *
     * @template TValue
     *
     * @param list<TValue> $values <p>
     * The values used to initialize the tuple.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Tuple<TValue> A tuple containing the provided values.
     */
    public function arr (array $values):Tuple {

        return new Tuple(
            new FixedStorage(
                Runtime\Arr\Inspection::count($values),
                new ArrayInit($values)
            )
        );

    }

    /**
     * ### Creates a tuple from an array callback
     *
     * Creates a new tuple containing the values returned by the provided callback.
     *
     * The storage capacity must be specified explicitly because the number of values returned by the callback is
     * not known until the callback is evaluated.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\ArrayCallbackInit To initialize the storage
     * using an array-producing callback.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage To provide fixed-size storage.
     *
     * @template TValue
     *
     * @param non-negative-int $capacity <p>
     * The maximum number of values the tuple storage can contain.
     * </p>
     * @param Closure():list<TValue> $callback <p>
     * The callback that returns the values used to initialize the tuple.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Tuple<TValue> A tuple containing the values returned by the
     * callback.
     */
    public function arrCallback (int $capacity, Closure $callback):Tuple {

        return new Tuple(
            new FixedStorage(
                $capacity,
                new ArrayCallbackInit($callback)
            )
        );

    }

    /**
     * ### Creates a tuple from a generator callback
     *
     * Creates a new tuple containing the values produced by a generator returned from the provided callback.
     *
     * The storage capacity must be specified explicitly because the number of generated values is not known until
     * the generator is consumed.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\GeneratorCallbackInit To initialize the
     * storage using a generator-producing callback.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage To provide fixed-size storage.
     *
     * @template TValue
     *
     * @param non-negative-int $capacity <p>
     * The maximum number of values the tuple storage can contain.
     * </p>
     * @param Closure():Generator<int, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial tuple values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Tuple<TValue> A tuple containing the generated values.
     */
    public function generator (int $capacity, Closure $callback):Tuple {

        return new Tuple(
            new FixedStorage(
                $capacity,
                new GeneratorCallbackInit($callback)
            )
        );

    }

    /**
     * ### Creates a tuple filled with a value
     *
     * Creates a new tuple containing the specified value repeated for the requested length.
     *
     * The tuple storage capacity is equal to the specified length.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\FillInit To initialize the storage with
     * repeated values.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage To provide fixed-size storage.
     *
     * @template TValue
     *
     * @param non-negative-int $length <p>
     * The number of values to initialize.
     * </p>
     * @param TValue $value <p>
     * The value used to fill the tuple.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Tuple<TValue> A tuple filled with the specified value.
     */
    public function fill (int $length, mixed $value):Tuple {

        return new Tuple(
            new FixedStorage(
                $length,
                new FillInit($length, $value)
            )
        );

    }

    /**
     * ### Creates a tuple from a numeric range
     *
     * Creates a new tuple containing the values produced by the specified numeric range.
     *
     * The storage capacity is automatically calculated from the range boundaries and step.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\RangeInit To initialize the storage with the
     * numeric range.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage To provide fixed-size storage.
     * @uses \FireHub\Runtime\Math::floor() To round down the range length.
     *
     * @param int|float $start <p>
     * The first value of the range.
     * </p>
     * @param int|float $end <p>
     * The last value of the range.
     * </p>
     * @param int|float $step [optional] <p>
     * The amount by which each subsequent value is incremented.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Tuple<int|float> A tuple containing the generated numeric range.
     */
    public function range (int|float $start, int|float $end, int|float $step = 1):Tuple {

        /** @var non-negative-int $capacity */
        $capacity = $start <= $end && $step > 0
            ? Runtime\Math::floor(($end - $start) / $step) + 1
            : 0;

        return new Tuple(
            new FixedStorage(
                $capacity,
                new RangeInit($start, $end, $step)
            )
        );

    }

}