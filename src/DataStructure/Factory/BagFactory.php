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

use FireHub\Foundation\DataStructure\Bag;
use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\Strategy;

/**
 * ### Provides factory methods for creating bag data structures
 *
 * Provides convenient factory methods for creating bags backed by hash-based bag storage.
 *
 * The hashing and equality semantics of stored values are defined by the configured hash strategy, while the
 * underlying storage maintains the multiplicity of logically equal values.
 * @since 1.0.0
 */
readonly class BagFactory {

    /**
     * ### Creates an empty bag
     *
     * Creates a new empty bag using the specified hashing and equality strategy.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashBagStorage To provide hash-based bag storage.
     *
     * @template TValue
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TValue> $strategy <p>
     * The hashing and equality strategy used for bag values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Bag<TValue> An empty bag.
     */
    public function empty (Strategy $strategy):Bag {

        return new Bag(
            new HashBagStorage($strategy)
        );

    }

}