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

use FireHub\Foundation\DataStructure\Struct;
use FireHub\Foundation\DataStructure\Storage\HashStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash;
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    ArrayCallbackInit, ArrayInit, EmptyInit, FillInit, GeneratorCallbackInit, RangeInit
};
use Closure, Generator;

/**
 * ### Provides factory methods for creating struct data structures
 *
 * Provides convenient factory methods for creating fixed-structure keyed records backed by native array hash
 * storage.
 *
 * Struct keys are defined during initialization and form the fixed structure of the resulting record. Integer and
 * string keys are supported according to the native PHP array-key semantics provided by the underlying hash
 * storage.
 * @since 1.0.0
 */
readonly class StructFactory {

    /**
     * ### Creates a struct from an array
     *
     * Creates a new struct containing the key-value associations provided by the specified array.
     *
     * The array keys define the fixed structure of the resulting Struct.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit To initialize the storage with the
     * provided key-value associations.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide native array hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To provide keyed storage.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param array<TKey, TValue> $values <p>
     * The key-value associations used to initialize the struct.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Struct<TKey, TValue> A struct containing the provided key-value
     * associations.
     */
    public function arr (array $values):Struct {

        return new Struct(
            new HashStorage(
                new ArrHash(
                    new ArrayInit($values)
                )
            )
        );

    }

    /**
     * ### Creates a struct from an array callback
     *
     * Creates a new struct containing the key-value associations returned by the provided callback.
     *
     * The returned array keys define the fixed structure of the resulting Struct.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\ArrayCallbackInit To initialize the storage
     * using an array-producing callback.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide native array hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To provide keyed storage.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param Closure():array<TKey, TValue> $callback <p>
     * The callback that returns the key-value associations used to initialize the struct.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Struct<TKey, TValue> A struct containing the key-value associations
     * returned by the callback.
     */
    public function arrCallback (Closure $callback):Struct {

        return new Struct(
            new HashStorage(
                new ArrHash(
                    new ArrayCallbackInit($callback)
                )
            )
        );

    }

    /**
     * ### Creates a struct from a generator callback
     *
     * Creates a new struct containing the key-value associations produced by a generator returned from the provided
     * callback.
     *
     * Generated keys define the fixed structure of the resulting Struct and must be valid native PHP array keys.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\GeneratorCallbackInit To initialize the
     * storage using a generator-producing callback.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide native array hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To provide keyed storage.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param Closure():Generator<TKey, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial struct key-value associations.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Struct<TKey, TValue> A struct containing the generated key-value
     * associations.
     */
    public function generator (Closure $callback):Struct {

        return new Struct(
            new HashStorage(
                new ArrHash(
                    new GeneratorCallbackInit($callback)
                )
            )
        );

    }

    /**
     * ### Creates a struct filled with a value
     *
     * Creates a new struct containing the specified value repeated for the requested length.
     *
     * Integer keys starting from zero define the resulting Struct structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\FillInit To initialize the storage with
     * repeated values.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide native array hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To provide keyed storage.
     *
     * @template TValue
     *
     * @param non-negative-int $length <p>
     * The number of elements to initialize.
     * </p>
     * @param TValue $value <p>
     * The value used to fill the struct.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Struct<int, TValue> A struct filled with the specified value.
     */
    public function fill (
        int $length,
        mixed $value
    ):Struct {

        return new Struct(
            new HashStorage(
                new ArrHash(
                    new FillInit($length, $value)
                )
            )
        );

    }

    /**
     * ### Creates a struct from a numeric range
     *
     * Creates a new struct containing the values produced by the specified numeric range.
     *
     * Integer keys starting from zero define the resulting Struct structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\RangeInit To initialize the storage with the
     * numeric range.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide native array hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To provide keyed storage.
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
     * @return \FireHub\Foundation\DataStructure\Struct<int, int|float> A struct containing the generated numeric
     * range.
     */
    public function range (int|float $start, int|float $end, int|float $step = 1):Struct {

        return new Struct(
            new HashStorage(
                new ArrHash(
                    new RangeInit($start, $end, $step)
                )
            )
        );

    }

}