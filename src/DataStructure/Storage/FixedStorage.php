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
use FireHub\Foundation\DataStructure\Storage\Capability\ {
    Capacity, Metrics, LinearBoundaryAccess
};
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Foundation\DataStructure\Exception\OverflowException;
use SplFixedArray;

/**
 * ### Provides a storage implementation with a fixed size
 *
 * Fixed storage maintains values in a zero-based indexed sequence backed by a fixed-size array. The storage size
 * is established during initialization and cannot be changed after construction, making it suitable for data
 * structures that require a predetermined number of indexed positions.
 *
 * The implementation manages the fixed-size underlying representation while the consuming data structure defines
 * the public API and semantics exposed to its users.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<int, null|TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\LinearBoundaryAccess<null|TValue>
 */
final class FixedStorage implements Storage, Metrics, Capacity, LinearBoundaryAccess {

    /**
     * ### Underlying fixed-size data storage
     * @since 1.0.0
     *
     * @var SplFixedArray<null|TValue>
     */
    private SplFixedArray $data;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initializer::initialize() To initialize the storage.
     *
     * @param int $size <p>
     * The fixed number of positions in the storage.
     * </p>
     * @param Initializer<int, TValue> $initializer <p>
     * Initializes the storage with the provided values.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\OverflowException If the initializer contains more values
     * than the storage size allows.
     *
     * @return void
     */
    public function __construct (int $size, Initializer $initializer) {

        $this->data = new SplFixedArray($size);

        $key = 0;

        foreach ($initializer->initialize() as $value) {

            if ($key >= $size)
                throw new OverflowException(
                    'The initializer contains more values than the storage size allows.'
                );

            $this->data[$key++] = $value;

        }

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
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::size() To get the size of the storage.
     */
    public function isEmpty ():bool {

        return $this->size() === 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::remainingCapacity() To get the remaining
     * capacity of the storage.
     */
    public function isFull ():bool {

        return $this->remainingCapacity() === 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function size ():int {

        $size = 0;

        foreach ($this->data as $value)
            if ($value !== null)
                $size++;

        return $size;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function capacity ():int {

        /** @var non-negative-int */
        return $this->data->getSize();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::capacity() To get the capacity of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::size() To get the size of the storage.
     */
    public function remainingCapacity ():int {

        /** @var non-negative-int */
        return $this->capacity() - $this->size();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     *
     * @return \FireHub\Foundation\Maybe\Some<null|TValue>|\FireHub\Foundation\Maybe\None The first value, or an empty
     * maybe if the storage contains no values.
     */
    public function first ():Some|None {

        return $this->isEmpty()
            ? new None()
            : new Some($this->data[0]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::size() To get the size of the storage.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     *
     * @return \FireHub\Foundation\Maybe\Some<null|TValue>|\FireHub\Foundation\Maybe\None The last value, or an empty
     * maybe if the storage contains no values.
     */
    public function last ():Some|None {

        return $this->isEmpty()
            ? new None()
            : new Some($this->data[$this->size() - 1]);

    }

}