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

use FireHub\Foundation\DataStructure\Map;
use FireHub\Foundation\DataStructure\Storage\HashStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\ {
    Engine\ArrHash,
    Strategy, Strategy\MixedHashStrategy
};
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    ArrayCallbackInit, ArrayInit, EmptyInit, GeneratorCallbackInit
};
use Closure, Generator;

/**
 * ### Provides factory methods for creating map data structures
 *
 * Provides convenient factory methods for creating maps using the Foundation data structure implementation.
 *
 * Maps created directly through this factory use the native array-backed hash engine. A bucket-based hash engine
 * may be selected through the bucket factory when custom hashing and equality semantics or non-array key types are
 * required.
 * @since 1.0.0
 */
readonly class MapFactory {

    /**
     * ### Creates an empty map
     *
     * Creates a new empty map backed by the native array hash engine.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit To initialize an empty storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide array-backed hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To store the map key-value associations.
     *
     * @return \FireHub\Foundation\DataStructure\Map<array-key, mixed> An empty map.
     */
    public function empty ():Map {

        return new Map(
            new HashStorage(
                new ArrHash(
                    new EmptyInit
                )
            )
        );

    }

    /**
     * ### Creates a map from an array
     *
     * Creates a new map containing the key-value associations provided by the specified array.
     *
     * The map uses the native array hash engine and therefore supports integer and string keys.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit To initialize storage with the
     * provided key-value associations.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide array-backed hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To store the map key-value associations.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param array<TKey, TValue> $values <p>
     * The key-value associations to initialize the map with.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Map<TKey, TValue> A map containing the provided key-value
     * associations.
     */
    public function arr (array $values):Map {

        return new Map(
            new HashStorage(
                new ArrHash(
                    new ArrayInit($values)
                )
            )
        );

    }

    /**
     * ### Creates a map from an array callback
     *
     * Creates a new map using the key-value associations returned by the provided callback.
     *
     * The map uses the native array hash engine and therefore supports integer and string keys.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\ArrayCallbackInit To initialize storage using
     * an array-producing callback.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide array-backed hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To store the map key-value associations.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param Closure():array<TKey, TValue> $callback <p>
     * The callback that returns the key-value associations used to initialize the map.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Map<TKey, TValue> A map containing the key-value associations
     * returned by the callback.
     */
    public function arrCallback (Closure $callback):Map {

        return new Map(
            new HashStorage(
                new ArrHash(
                    new ArrayCallbackInit($callback)
                )
            )
        );

    }

    /**
     * ### Creates a map from a generator callback
     *
     * Creates a new map using key-value associations produced by a generator returned from the provided callback.
     *
     * The map uses the native array hash engine and therefore requires yielded keys to be valid native array keys.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\GeneratorCallbackInit To initialize storage
     * using a generator-producing callback.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide array-backed hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage To store the map key-value associations.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param Closure():Generator<TKey, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial map key-value associations.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Map<TKey, TValue> A map containing the generated key-value
     * associations.
     */
    public function generator (Closure $callback):Map {

        return new Map(
            new HashStorage(
                new ArrHash(
                    new GeneratorCallbackInit($callback)
                )
            )
        );

    }

    /**
     * ### Selects bucket-based hash storage
     *
     * Creates a specialized map factory that uses bucket-based hashing.
     *
     * Bucket-based hashing allows custom hashing and equality semantics and supports key types that cannot be
     * represented directly as native PHP array keys.
     * @since 1.0.0
     *
     * @template TKey
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TKey> $strategy [optional] <p>
     * The hashing and equality strategy used for map keys.
     *
     * If not specified, the mixed hashing strategy is used.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Factory\BucketMapFactory<TKey> The bucket-based map factory.
     */
    public function bucket (Strategy $strategy = new MixedHashStrategy()):BucketMapFactory {

        return new BucketMapFactory($strategy);

    }

}