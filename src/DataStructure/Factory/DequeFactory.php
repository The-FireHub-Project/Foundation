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

namespace FireHub\Foundation\DataStructure\Factory;

use FireHub\Foundation\DataStructure\Deque;
use FireHub\Foundation\DataStructure\Storage\ListStorage;
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    ArrayCallbackInit, ArrayInit, EmptyInit, FillInit, GeneratorCallbackInit, RangeInit
};
use Closure, Generator;

/**
 * ### Provides factory methods for creating deque data structures
 *
 * Provides convenient factory methods for creating deque using the Foundation data structure implementation.
 *
 * The factory encapsulates the storage and initialization details required to construct a deque, allowing callers
 * to create deque without directly depending on the underlying storage implementation or its initialization
 * strategies.
 *
 * Deque created by this factory use list storage as their underlying sequential storage implementation.
 * @since 1.0.0
 */
readonly class DequeFactory {

    /**
     * ### Creates an empty deque
     *
     * Creates a new deque containing no values.
     *
     * The deque is initialized using an empty initialization strategy and backed by list storage, making it ready
     * for later double-ended mutation and access operations.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit To initialize an empty storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage To store the deque values sequentially.
     *
     * @return \FireHub\Foundation\DataStructure\Deque<mixed> An empty deque.
     */
    public function empty ():Deque {

        return new Deque(
            new ListStorage(new EmptyInit())
        );

    }

    /**
     * ### Creates a deque from an array of values
     *
     * Creates a new deque containing the values provided by the specified array.
     *
     * The supplied values are initialized into list storage and retain their sequential ordering within the
     * resulting deque.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit To initialize storage with the
     * provided values.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage To store the deque values sequentially.
     *
     * @template TValue
     *
     * @param list<TValue> $values <p>
     * The values to initialize the deque with.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Deque<TValue> A deque containing the provided values.
     */
    public function arr (array $values):Deque {

        return new Deque(
            new ListStorage(new ArrayInit($values))
        );

    }

    /**
     * ### Creates a deque from an array callback
     *
     * Creates a new deque using the array returned by the provided callback.
     *
     * The callback is invoked during storage initialization, and its returned values are used as the initial
     * contents of the deque.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\ArrayCallbackInit To initialize storage using
     * an array-producing callback.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage To store the deque values sequentially.
     *
     * @template TValue
     *
     * @param Closure():list<TValue> $callback <p>
     * The callback that returns the values used to initialize the deque.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Deque<TValue> A deque containing the values returned by the
     * callback.
     */
    public function arrCallback (Closure $callback):Deque {

        return new Deque(
            new ListStorage(new ArrayCallbackInit($callback))
        );

    }

    /**
     * ### Creates a deque from a generator callback
     *
     * Creates a new deque using values produced by a generator returned from the provided callback.
     *
     * The callback creates a generator during storage initialization. Values yielded by the generator are consumed
     * sequentially and used as the initial contents of the deque.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\GeneratorCallbackInit To initialize storage
     * using a generator-producing callback.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage To store the deque values sequentially.
     *
     * @template TValue
     *
     * @param Closure():Generator<int, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial deque values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Deque<TValue> A deque containing the values produced by the
     * generator.
     */
    public function generator (Closure $callback):Deque {

        return new Deque(
            new ListStorage(new GeneratorCallbackInit($callback))
        );

    }

    /**
     * ### Creates a deque filled with a repeated value
     *
     * Creates a new deque containing the specified number of elements, with each element containing the provided
     * value.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\FillInit To generate the repeated initial
     * values.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage To store the deque values sequentially.
     *
     * @template TValue
     *
     * @param int $length <p>
     * The number of elements to create.
     * </p>
     * @param TValue $value <p>
     * The value to assign to each element.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Deque<TValue> A deque containing the repeated value.
     */
    public function fill (int $length, mixed $value):Deque {

        return new Deque(
            new ListStorage(new FillInit($length, $value))
        );

    }

    /**
     * ### Creates a deque from a range of values
     *
     * Creates a new deque containing a sequential series of numeric values beginning with the specified start
     * value and continuing toward the specified end value using the provided step.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\RangeInit To generate the sequential range of
     * values.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage To store the deque values sequentially.
     *
     * @template TValue of int|float
     *
     * @param TValue $start <p>
     * The first value of the sequence.
     * </p>
     * @param TValue $end <p>
     * The value at which the sequence ends.
     * </p>
     * @param TValue $step [optional] <p>
     * The increment between consecutive values in the sequence.
     *
     * If not specified, the step defaults to 1.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Deque<TValue> A deque containing the generated range of values.
     */
    public function range (int|float $start, int|float $end, int|float $step = 1):Deque {

        return new Deque(
            new ListStorage(new RangeInit($start, $end, $step))
        );

    }

}