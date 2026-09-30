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

use FireHub\Foundation\DataStructure\Bag;
use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\ {
    Engine\BucketHash,
    Strategy
};
use FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit;
use Closure, Generator;

/**
 * ### Provides factory methods for creating bucket-based bag data structures
 *
 * Provides convenient factory methods for creating bags backed by bucket-based hash storage.
 *
 * Bucket-based hashing separates value hashing and equality semantics from the underlying storage representation,
 * allowing bags to support arbitrary value types according to the configured hashing strategy.
 *
 * Each distinct bag value is represented as a key within the underlying hash engine, while the associated hash
 * value represents the number of occurrences of that value.
 * @since 1.0.0
 *
 * @template TValue
 */
readonly class BucketBagFactory {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TValue> $strategy <p>
     * The hashing and equality strategy used for bag values.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Strategy $strategy
    ) {}

    /**
     * ### Creates an empty bucket-based bag
     *
     * Creates a new empty bag using the configured hashing and equality strategy.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Bag<mixed> An empty bucket-based bag.
     */
    public function empty ():Bag {

        /** @var \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash<mixed, positive-int> $hash */
        $hash = new BucketHash(
            new EmptyInit,
            $this->strategy
        );

        return new Bag(
            new HashBagStorage(
                $hash
            )
        );

    }

    /**
     * ### Creates a bucket-based bag from an array
     *
     * Creates a new bag containing the values provided by the specified array.
     *
     * Duplicate values according to the configured hashing and equality strategy increase the multiplicity of the
     * corresponding value within the resulting bag.
     * @since 1.0.0
     *
     * @param array<array-key, TValue> $values <p>
     * The values to initialize the bag with.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Bag<TValue> A bucket-based bag containing the provided values.
     */
    public function arr (array $values):Bag {

        $bag = $this->empty();

        foreach ($values as $value)
            $bag->add($value);

        /** @var \FireHub\Foundation\DataStructure\Bag<TValue> $bag */
        return $bag;

    }

    /**
     * ### Creates a bucket-based bag from an array callback
     *
     * Creates a new bag containing the values returned by the provided callback.
     *
     * Duplicate values according to the configured hashing and equality strategy increase the multiplicity of the
     * corresponding value within the resulting bag.
     * @since 1.0.0
     *
     * @param Closure():array<array-key, TValue> $callback <p>
     * The callback that returns the values used to initialize the bag.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Bag<TValue> A bucket-based bag containing the returned values.
     */
    public function arrCallback (Closure $callback):Bag {

        return $this->arr(
            $callback()
        );

    }

    /**
     * ### Creates a bucket-based bag from a generator callback
     *
     * Creates a new bag using values produced by a generator returned from the provided callback.
     *
     * Generator keys are ignored. Duplicate values according to the configured hashing and equality strategy
     * increase the multiplicity of the corresponding value within the resulting bag.
     * @since 1.0.0
     *
     * @param Closure():Generator<mixed, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial bag values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Bag<TValue> A bucket-based bag containing the generated values.
     */
    public function generator (Closure $callback):Bag {

        $bag = $this->empty();

        foreach ($callback() as $value)
            $bag->add($value);

        /** @var \FireHub\Foundation\DataStructure\Bag<TValue> $bag */
        return $bag;

    }

}