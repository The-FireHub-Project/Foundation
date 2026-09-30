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

use FireHub\Foundation\DataStructure\Set;
use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\ {
    Engine\ArrHash,
    Strategy, Strategy\MixedHashStrategy
};
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    EmptyInit, GeneratorCallbackInit
};
use Closure, Generator;

/**
 * ### Provides factory methods for creating set data structures
 *
 * Provides convenient factory methods for creating sets using the Foundation data structure implementation.
 *
 * Sets created directly through this factory use the native array-backed hash engine and therefore support values
 * that can be represented as native PHP array keys. A bucket-based hash engine may be selected through the bucket
 * factory when custom hashing and equality semantics or non-array-key value types are required.
 * @since 1.0.0
 */
readonly class SetFactory {

    /**
     * ### Creates an empty set
     *
     * Creates a new empty set backed by the native array hash engine.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit To initialize an empty storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide array-backed hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashSetStorage To store unique set values.
     *
     * @return \FireHub\Foundation\DataStructure\Set<array-key> An empty set.
     */
    public function empty ():Set {

        /** @var ArrHash<array-key, true> $hash */
        $hash = new ArrHash(
            new EmptyInit
        );

        return new Set(
            new HashSetStorage(
                $hash
            )
        );

    }

    /**
     * ### Creates a set from an array
     *
     * Creates a new set containing the values provided by the specified array.
     *
     * The set uses the native array hash engine and therefore requires values to be valid native PHP array keys.
     * Duplicate values are represented only once in the resulting set.
     * @since 1.0.0
     *
     * @template TValue of array-key
     *
     * @param array<array-key, TValue> $values <p>
     * The values to initialize the set with.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Set<TValue> A set containing the provided values.
     */
    public function arr (array $values):Set {

        return $this->generator( // @phpstan-ignore argument.templateType
            static function () use ($values):Generator { // @phpstan-ignore argument.type

                foreach ($values as $value)
                    yield $value => true;

            }
        );

    }

    /**
     * ### Creates a set from an array callback
     *
     * Creates a new set containing the values returned by the provided callback.
     *
     * The set uses the native array hash engine and therefore requires returned values to be valid native PHP array
     * keys. Duplicate values are represented only once in the resulting set.
     * @since 1.0.0
     *
     * @template TValue of array-key
     *
     * @param Closure():array<array-key, TValue> $callback <p>
     * The callback that returns the values used to initialize the set.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Set<TValue> A set containing the values returned by the callback.
     */
    public function arrCallback (Closure $callback):Set {

        return $this->arr(
            $callback()
        );

    }

    /**
     * ### Creates a set from a generator callback
     *
     * Creates a new set using values produced by a generator returned from the provided callback.
     *
     * The set uses the native array hash engine and therefore requires generated values to be valid native PHP
     * array keys. Generator keys are ignored.
     * @since 1.0.0
     *
     * @template TValue of array-key
     *
     * @param Closure():Generator<mixed, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial set values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Set<TValue> A set containing the generated values.
     */
    public function generator (Closure $callback):Set {

        /** @var ArrHash<array-key, true> $hash */
        $hash = new ArrHash(
            new GeneratorCallbackInit(
                static function () use ($callback):Generator {

                    foreach ($callback() as $value)
                        yield $value => true;

                }
            )
        );

        return new Set(
            new HashSetStorage(
                $hash
            )
        );

    }

    /**
     * ### Selects bucket-based hash storage
     *
     * Creates a specialized set factory that uses bucket-based hashing.
     *
     * Bucket-based hashing allows custom hashing and equality semantics and supports value types that cannot be
     * represented directly as native PHP array keys.
     * @since 1.0.0
     *
     * @template TValue
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TValue> $strategy [optional] <p>
     * The hashing and equality strategy used for set values.
     *
     * If not specified, the mixed hashing strategy is used.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Factory\BucketSetFactory<TValue> The bucket-based set factory.
     */
    public function bucket (Strategy $strategy = new MixedHashStrategy()):BucketSetFactory {

        return new BucketSetFactory($strategy);

    }

}