<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Storage;

use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Capability\Metrics;
use FireHub\Runtime;

/**
 * ### Provides a mutable storage implementation for sequentially organized values
 *
 * List storage maintains values in a zero-based indexed sequence and provides the fundamental storage operations
 * required by data structures that organize their values linearly. Values retain their relative order and can be
 * accessed and modified according to their position within the sequence.
 *
 * The implementation is designed as a general-purpose linear storage mechanism and does not impose the public
 * semantics of a particular data structure. Higher-level structures such as vectors, stacks, queues, and deque
 * may use list storage according to the capabilities they require.
 *
 * List storage manages the underlying representation and storage behavior while the consuming data structure
 * defines the public API and semantics exposed to its users.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<int, TValue>
 */
final class ListStorage implements Storage, Metrics {

    /**
     * ### Underlying data storage
     * @since 1.0.0
     *
     * @var array<int, TValue>
     */
    private array $data;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initializer::initialize() To initialize the storage.
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the array.
     * @uses \FireHub\Runtime\Iterator::toArray() To convert the iterator to an array.
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Initializer<int, TValue> $initializer <p>
     * Initializes the storage with the provided values.
     * </p>
     *
     * @return void
     */
    public function __construct (Initializer $initializer) {

        $this->data = Runtime\Arr\Access::values(
            Runtime\Iterator::toArray($initializer->initialize())
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function iterate ():iterable {

        return $this->data;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::size() To get the size of the storage.
     */
    public function isEmpty ():bool {

        return $this->size() === 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the size of the storage.
     */
    public function size ():int {

        return Runtime\Arr\Inspection::count($this->data);

    }

}