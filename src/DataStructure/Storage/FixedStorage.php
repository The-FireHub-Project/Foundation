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

namespace FireHub\Foundation\DataStructure\Storage;

use FireHub\Core\Boundary\Capability\ {
    Access\BoundaryAccess, Access\IndexAccess,
    Measurement\Capacity, Measurement\Metrics,
    Mutation\IndexMutation,
    Transformation\Mappable,
    Cloneable, Forkable
};
use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit;
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Foundation\State\ {
    HasCopyOnWriteState, SharedState
};
use FireHub\Foundation\DataStructure\Exception\OverflowException;
use FireHub\Runtime;
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
 * @implements \FireHub\Core\Boundary\Capability\Access\BoundaryAccess<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Access\IndexAccess<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\IndexMutation<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<int, TValue>
 *
 * @phpstan-type State SplFixedArray<null|TValue>
 */
final class FixedStorage implements Storage, Cloneable, Forkable, Metrics, Capacity, BoundaryAccess, IndexAccess,
    IndexMutation, Mappable {

    /**
     * ### Copy-on-write state
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\State\HasCopyOnWriteState<State>
     */
    use HasCopyOnWriteState;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initializer::initialize() To initialize the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     *
     * @param non-negative-int $size <p>
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

        /** @var State $data */
        $data = new SplFixedArray($size);

        $this->state = new SharedState($data);

        $key = 0;

        foreach ($initializer->initialize() as $value) {

            if ($key >= $size)
                throw new OverflowException(
                    'The initializer contains more values than the storage size allows.'
                );

            $data[$key++] = $value;

        }

        $this->state = new SharedState($data);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::capacity() To get the capacity of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit To initialize the storage with an empty
     * array.
     */
    public function emptyCopy ():self {

        return new self($this->capacity(), new EmptyInit);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Copy::deep() To deep copy the storage.
     *
     * @throws \FireHub\Runtime\Exception\CopyObjectException If the object's copying fails.
     */
    protected function copyData (mixed $data):SplFixedArray {

        $copy = new SplFixedArray($this->state->data()->getSize());

        foreach ($data as $index => $value)
            $copy[$index] = Runtime\Copy::deep($value);

        /** @var State */
        return $copy;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function iterate ():iterable {

        yield from $this->state->data();

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
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function size ():int {

        $size = 0;

        foreach ($this->state->data() as $value)
            if ($value !== null)
                $size++;

        return $size;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function capacity ():int {

        /** @var non-negative-int */
        return $this->state->data()->getSize();

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
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function first ():Maybe {

        $data = $this->state->data();

        foreach ($data as $value)
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
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function last ():Maybe {

        $data = $this->state->data();

        for ($index = $this->capacity() - 1; $index >= 0; $index--)
            if ($data[$index] !== null)
                return new Some($this->state->data()[$index]);

        return new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::capacity() To get the capacity of the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function has (int $index):bool {

        $data = $this->state->data();

        return $index >= 0
            && $index < $this->capacity()
            && $data[$index] !== null;

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
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function get (int $index):Maybe {

        /** @var \FireHub\Foundation\Maybe\Some<TValue>|\FireHub\Foundation\Maybe\None */
        return $this->has($index) // @phpstan-ignore varTag.type
            ? new Some($this->state->data()[$index])
            : new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::capacity() To get the capacity of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::detach() To detach the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function set (int $index, mixed $value):MutationOutcome {

        if ($index < 0 || $index >= $this->capacity())
            return MutationOutcome::NOT_FOUND;

        $this->detach();

        $outcome = $this->state->data()[$index] === null
            ? MutationOutcome::CREATED
            : MutationOutcome::UPDATED;

        $this->state->data()[$index] = $value;

        return $outcome;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::has() To check if the storage has a value at
     * the specified index.
     * @uses \FireHub\Foundation\DataStructure\Storage\FixedStorage::detach() To detach the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function remove (int $index):MutationOutcome {

        if (!$this->has($index))
            return MutationOutcome::NOT_FOUND;

        $this->detach();

        $data = &$this->state->data();

        unset($data[$index]);

        return MutationOutcome::REMOVED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function map (callable $callback):self {

        $source = $this->state->data();
        $mapped = new SplFixedArray($source->getSize());

        foreach ($source as $index => $value)
            if ($value !== null)
                $mapped[$index] = $callback($value, $index);

        return clone($this, [ // @phpstan-ignore assign.propertyType
            'state' => new SharedState($mapped)
        ]);

    }

}