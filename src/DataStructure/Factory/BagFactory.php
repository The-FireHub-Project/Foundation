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
    Engine\ArrHash,
    Strategy, Strategy\MixedHashStrategy
};
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    ArrayInit, EmptyInit
};
use Closure;

/**
 * ### Provides factory methods for creating bag data structures
 *
 * Provides convenient factory methods for creating bags using the Foundation data structure implementation.
 *
 * Bags created directly through this factory use the native array-backed hash engine and therefore support values
 * that can be represented as native PHP array keys. Each distinct value is stored as a hash key, while the
 * associated hash value represents the number of occurrences of that value.
 *
 * A bucket-based hash engine may be selected through the bucket factory when custom hashing and equality semantics
 * or non-array-key value types are required.
 * @since 1.0.0
 */
readonly class BagFactory {

    /**
     * ### Creates an empty bag
     *
     * Creates a new empty bag backed by the native array hash engine.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit To initialize an empty storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash To provide array-backed hash storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\HashBagStorage To store bag values and their multiplicities.
     *
     * @return \FireHub\Foundation\DataStructure\Bag<array-key> An empty bag.
     */
    public function empty ():Bag {

        /** @var \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash<array-key, positive-int> $hash */
        $hash = new ArrHash(
            new EmptyInit
        );

        return new Bag(
            new HashBagStorage(
                $hash
            )
        );

    }

    /**
     * ### Creates a bag from an array
     *
     * Creates a new bag containing the values provided by the specified array.
     *
     * The bag uses the native array hash engine and therefore requires values to be valid native PHP array keys.
     * Duplicate values increase the multiplicity of the corresponding value within the resulting bag.
     * @since 1.0.0
     *
     * @template TValue of array-key
     *
     * @param array<array-key, TValue> $values <p>
     * The values to initialize the bag with.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Bag<TValue> A bag containing the provided values.
     */
    public function arr (array $values):Bag {

        /** @var array<TValue, positive-int> $frequencies */
        $frequencies = [];

        foreach ($values as $value)
            $frequencies[$value] = ($frequencies[$value] ?? 0) + 1;

        return new Bag(
            new HashBagStorage(
                new ArrHash(
                    new ArrayInit($frequencies)
                ),
                count($values)
            )
        );

    }

    /**
     * ### Creates a bag from an array callback
     *
     * Creates a new bag containing the values returned by the provided callback.
     *
     * The bag uses the native array hash engine and therefore requires returned values to be valid native PHP array
     * keys. Duplicate values increase the multiplicity of the corresponding value within the resulting bag.
     * @since 1.0.0
     *
     * @template TValue of array-key
     *
     * @param Closure():array<array-key, TValue> $callback <p>
     * The callback that returns the values used to initialize the bag.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Bag<TValue> A bag containing the values returned by the callback.
     */
    public function arrCallback (Closure $callback):Bag {

        return $this->arr(
            $callback()
        );

    }

    /**
     * ### Creates a bag from a generator callback
     *
     * Creates a new bag using values produced by a generator returned from the provided callback.
     *
     * The bag uses the native array hash engine and therefore requires generated values to be valid native PHP
     * array keys. Generator keys are ignored, and duplicate values increase the multiplicity of the corresponding
     * value within the resulting bag.
     * @since 1.0.0
     *
     * @template TValue of array-key
     *
     * @param Closure():\Generator<mixed, TValue> $callback <p>
     * The callback that returns a generator used to produce the initial bag values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Bag<TValue> A bag containing the generated values.
     */
    public function generator (Closure $callback):Bag {

        /** @var array<TValue, positive-int> $frequencies */
        $frequencies = [];

        $size = 0;

        foreach ($callback() as $value) {

            $frequencies[$value] = ($frequencies[$value] ?? 0) + 1;

            $size++;

        }

        return new Bag(
            new HashBagStorage(
                new ArrHash(
                    new ArrayInit($frequencies)
                ),
                $size
            )
        );

    }

    /**
     * ### Selects bucket-based hash storage
     *
     * Creates a specialized bag factory that uses bucket-based hashing.
     *
     * Bucket-based hashing allows custom hashing and equality semantics and supports value types that cannot be
     * represented directly as native PHP array keys.
     * @since 1.0.0
     *
     * @template TValue
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TValue> $strategy [optional] <p>
     * The hashing and equality strategy used for bag values.
     *
     * If not specified, the mixed hashing strategy is used.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Factory\BucketBagFactory<TValue> The bucket-based bag factory.
     */
    public function bucket (Strategy $strategy = new MixedHashStrategy()):BucketBagFactory {

        return new BucketBagFactory($strategy);

    }

}