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

namespace FireHub\Foundation\DataStructure\Storage;

use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Capability\Metrics;
use FireHub\Runtime;

/**
 * ### Provides a storage implementation for lazily generated values
 *
 * Lazy storage retains an initialization strategy rather than materializing its values into an internal collection.
 * Values are obtained from the initializer when the storage is iterated, allowing potentially large or unbounded
 * data sources to be processed without requiring all values to be held in memory at once.
 *
 * The implementation is designed as a general-purpose lazy storage mechanism and does not impose the public
 * semantics of a particular data structure. Higher-level structures may use lazy storage when their operations
 * can be performed directly over the underlying initialization strategy.
 *
 * Lazy storage manages the deferred value generation while the consuming data structure defines the public API
 * and semantics exposed to its users.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<TKey, TValue>
 */
final readonly class LazyStorage implements Storage, Metrics {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Initializer<TKey, TValue> $initializer <p>
     * Initializes the storage with the provided values.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Initializer $initializer
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initializer::initialize() To initialize the storage.
     */
    public function iterate ():iterable {

        return $this->initializer->initialize();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\LazyStorage::size() To get the size of the storage.
     */
    public function isEmpty ():bool {

        return $this->size() === 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Iterator::count() To get the size of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\LazyStorage::iterate() To get the iterator.
     */
    public function size ():int {

        return Runtime\Iterator::count($this->iterate());

    }

}