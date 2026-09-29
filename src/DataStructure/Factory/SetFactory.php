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

use FireHub\Foundation\DataStructure\Set;
use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\Strategy;

/**
 * ### Provides factory methods for creating set data structures
 *
 * Provides convenient factory methods for creating sets backed by set storage.
 *
 * The hashing and equality semantics of stored values are defined by the configured hash strategy. Duplicate
 *  values, according to the selected strategy, are automatically discarded by the underlying storage.
 * @since 1.0.0
 */
readonly class SetFactory {

    /**
     * ### Creates an empty set
     *
     * Creates a new empty set using the specified hashing and equality strategy.
     * @since 1.0.0
     *
     * @template TValue
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TValue> $strategy <p>
     * The hashing and equality strategy used for set values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Set<TValue> An empty set.
     */
    public function empty (Strategy $strategy):Set {

        return new Set(
            new HashSetStorage($strategy)
        );

    }

}