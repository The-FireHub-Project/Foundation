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
    Engine\BucketHash,
    Strategy
};
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    EmptyInit, GeneratorCallbackInit
};
use Closure, Generator;

/**
 * ### Provides factory methods for creating bucket-based map data structures
 *
 * Provides convenient factory methods for creating maps backed by bucket-based hash storage.
 *
 * Bucket-based hashing separates key hashing and equality semantics from the underlying storage representation,
 * allowing maps to support arbitrary key types according to the configured hashing strategy.
 * @since 1.0.0
 *
 * @template TKey
 */
readonly class BucketMapFactory {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TKey> $strategy <p>
     * The hashing and equality strategy used for map keys.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Strategy $strategy
    ) {}

    /**
     * ### Creates an empty bucket-based map
     *
     * Creates a new empty map using the configured hashing and equality strategy.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Map<mixed, mixed> An empty bucket-based map.
     */
    public function empty ():Map {

        return new Map(
            new HashStorage(
                new BucketHash(
                    new EmptyInit,
                    $this->strategy
                )
            )
        );

    }

    /**
     * ### Creates a bucket-based map from a generator callback
     *
     * Creates a new map using key-value associations produced by a generator returned from the provided callback.
     *
     * Generator initialization allows arbitrary key types supported by the configured hashing strategy.
     * @since 1.0.0
     *
     * @template TValue
     *
     * @param Closure():Generator<TKey, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial key-value associations.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Map<TKey, TValue> A bucket-based map containing the generated
     * key-value associations.
     */
    public function generator (Closure $callback):Map {

        return new Map(
            new HashStorage(
                new BucketHash(
                    new GeneratorCallbackInit($callback),
                    $this->strategy
                )
            )
        );

    }

}