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
    Engine\BucketHash,
    Strategy
};
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    EmptyInit, GeneratorCallbackInit
};
use Closure, Generator;

/**
 * ### Provides factory methods for creating bucket-based set data structures
 *
 * Provides convenient factory methods for creating sets backed by bucket-based hash storage.
 *
 * Bucket-based hashing separates value hashing and equality semantics from the underlying storage representation,
 * allowing sets to support arbitrary value types according to the configured hashing strategy.
 * @since 1.0.0
 *
 * @template TValue
 */
readonly class BucketSetFactory {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TValue> $strategy <p>
     * The hashing and equality strategy used for set values.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Strategy $strategy
    ) {}

    /**
     * ### Creates an empty bucket-based set
     *
     * Creates a new empty set using the configured hashing and equality strategy.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Set<mixed> An empty bucket-based set.
     */
    public function empty ():Set {

        /** @var \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash<mixed, true> $hash */
        $hash = new BucketHash(
            new EmptyInit,
            $this->strategy
        );

        return new Set(
            new HashSetStorage(
                $hash
            )
        );

    }

    /**
     * ### Creates a bucket-based set from a generator callback
     *
     * Creates a new set using values produced by a generator returned from the provided callback.
     *
     * Generator keys are ignored. Each generated value is used as a hash key, and uniqueness is determined by the
     * configured hashing and equality strategy.
     * @since 1.0.0
     *
     * @param Closure():Generator<mixed, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial set values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Set<TValue> A bucket-based set containing the generated values.
     */
    public function generator (Closure $callback):Set {

        /** @var \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash<TValue, true> $hash */
        $hash = new BucketHash(
            new GeneratorCallbackInit(
                function () use ($callback):Generator {

                    foreach ($callback() as $value)
                        yield $value => true;

                }
            ),
            $this->strategy
        );

        return new Set(
            new HashSetStorage(
                $hash
            )
        );

    }

}
