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

use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Capability\ {
    Capacity, IndexAccess, IndexMutation, LinearBoundaryAccess, Metrics
};
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Foundation\DataStructure\Exception\OverflowException;
use SplFixedArray;

/**
 * ### Provides a storage implementation with a fixed size
 *
 * Fixed storage maintains values in a fixed number of indexed positions backed by a fixed-size array. The storage
 * capacity is established during initialization and cannot be changed after construction, making it suitable for data
 * structures that require a predetermined number of available positions.
 *
 * Fixed storage uses `null` to represent an unoccupied position. Consequently, `null` cannot be stored as a value.
 *
 * The implementation manages the fixed-size underlying representation while the consuming data structure defines
 * the public API, semantics, and structural invariants exposed to its users.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<int, null|TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\LinearBoundaryAccess<TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\IndexAccess<TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\IndexMutation<TValue>
 */
final class FixedStorage implements Storage, Metrics, Capacity, LinearBoundaryAccess, IndexAccess, IndexMutation {

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
     * @uses \FireHub\Foundation\Maybe\Some As the first value is always present.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     */
    public function first ():Maybe {

        foreach ($this->data as $value)
            if ($value !== null)
                return new Some($value);

        return new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::capacity() To get the capacity of the storage.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     */
    public function last ():Maybe {

        for ($index = $this->capacity() - 1; $index >= 0; $index--)
            if ($this->data[$index] !== null)
                return new Some($this->data[$index]);

        return new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::capacity() To get the capacity of the storage.
     */
    public function has (int $index):bool {

        return $index >= 0
            && $index < $this->capacity()
            && $this->data[$index] !== null;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::has() To check if the storage has a value at
     * the specified index.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     */
    public function get (int $index):Maybe {

        /** @var \FireHub\Foundation\Maybe\Some<TValue>|\FireHub\Foundation\Maybe\None */
        return $this->has($index) // @phpstan-ignore varTag.type
            ? new Some($this->data[$index])
            : new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function set (int $index, mixed $value):MutationOutcome {

        if ($index < 0 || $index >= $this->capacity())
            return MutationOutcome::NOT_FOUND;

        $outcome = $this->data[$index] === null
            ? MutationOutcome::CREATED
            : MutationOutcome::UPDATED;

        $this->data[$index] = $value;

        return $outcome;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::has() To check if the storage has a value at
     * the specified index.
     */
    public function remove (int $index):MutationOutcome {

        if (!$this->has($index))
            return MutationOutcome::NOT_FOUND;

        unset($this->data[$index]);

        return MutationOutcome::REMOVED;

    }

}