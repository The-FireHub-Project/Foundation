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
    Measurement\Metrics,
    Mutation\DequeMutation, Mutation\IndexMutation,
    Transformation\Filterable, Transformation\Mappable,
    Cloneable, Forkable
};
use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Foundation\State\ {
    HasCopyOnWriteState, SharedState
};
use FireHub\Runtime;

/**
 * ### Provides a mutable storage implementation for sequentially organized values
 *
 * List storage maintains values using integer indexes and preserves the relative order of its entries. It provides
 * fundamental storage operations for data structures that organize their values linearly, including indexed access
 * and mutation.
 *
 * The implementation is designed as a general-purpose linear storage mechanism and does not impose the public
 * semantics or structural invariants of a particular data structure. Higher-level structures such as vectors,
 * stacks, queues, and deque may use list storage according to the capabilities and invariants they require.
 *
 * List storage manages the underlying representation and storage behavior while the consuming data structure
 * defines the public API, semantics, and structural invariants exposed to its users.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Access\BoundaryAccess<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Access\IndexAccess<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\DequeMutation<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\IndexMutation<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Filterable<int, TValue>
 *
 * @phpstan-type State list<TValue>
 */
final class ListStorage implements Storage, Cloneable, Forkable, Metrics, BoundaryAccess, IndexAccess, DequeMutation,
    IndexMutation, Mappable, Filterable {

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

        $this->state = new SharedState(
            Runtime\Arr\Access::values(
                Runtime\Iterator::toArray($initializer->initialize())
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Copy::deep() To deep copy the storage.
     *
     * @throws \FireHub\Runtime\Exception\CopyObjectException If the object's copying fails.
     */
    protected function copyData (mixed $data):array {

        /** @var State */
        return Runtime\Copy::deep($data);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function iterate ():iterable {

        return $this->state->data();

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
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function size ():int {

        return Runtime\Arr\Inspection::count($this->state->data());

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     * @uses \FireHub\Runtime\Arr\Access::first() To get the first value.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function first ():Maybe {

        if ($this->isEmpty()) return new None();

        /** @var TValue $first */
        $first = Runtime\Arr\Access::first($this->state->data());

        return new Some($first);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     * @uses \FireHub\Runtime\Arr\Access::last() To get the last value.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function last ():Maybe {

        if ($this->isEmpty()) return new None();

        /** @var TValue $last */
        $last = Runtime\Arr\Access::last($this->state->data());

        return new Some($last);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Mutation::unshift() To insert values at the beginning of the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::detach() To detach the storage.
     */
    public function insertFront (mixed ...$values):void {

        if ($values === []) return;

        $this->detach();

        Runtime\Arr\Mutation::unshift($this->state->data(), ...$values);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Mutation::push() To insert values at the end of the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::detach() To detach the storage.
     */
    public function insertBack (mixed ...$values):void {

        if ($values === []) return;

        $this->detach();

        Runtime\Arr\Mutation::push($this->state->data(), ...$values);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::detach() To detach the storage.
     * @uses \FireHub\Runtime\Arr\Mutation::shift() To remove the first value from the storage.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function removeFront ():Maybe {

        if ($this->isEmpty()) return new None();

        $this->detach();

        /** @var TValue $shift */
        $shift = Runtime\Arr\Mutation::shift($this->state->data());

        return new Some($shift);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::detach() To detach the storage.
     * @uses \FireHub\Runtime\Arr\Mutation::pop() To remove the last value from the storage.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function removeBack ():Maybe {

        if ($this->isEmpty()) return new None();

        $this->detach();

        /** @var TValue $pop */
        $pop = Runtime\Arr\Mutation::pop($this->state->data());

        return new Some($pop);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::keyExists() To check if the storage has a value at the specified index.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function has (int $index):bool {

        return Runtime\Arr\Access::keyExists($this->state->data(), $index);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::has() To check if the storage has a value at
     * the specified index.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function get (int $index):Maybe {

        if ($this->has($index))
            return new Some($this->state->data()[$index]); // @phpstan-ignore offsetAccess.notFound

        return new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::has() To check if the storage has a value at
     * the specified index.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::detach() To detach the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function set (int $index, mixed $value):MutationOutcome {

        if (!$this->has($index))
            return MutationOutcome::NOT_FOUND;

        $this->detach();

        $this->state->data()[$index] = $value;

        return MutationOutcome::UPDATED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::has() To check if the storage has a value at
     * the specified index.
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::detach() To detach the storage.
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the array.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function remove (int $index):MutationOutcome {

        if (!$this->has($index))
            return MutationOutcome::NOT_FOUND;

        $this->detach();

        $data = &$this->state->data();

        unset($data[$index]);

        $data = Runtime\Arr\Access::values($data);

        return MutationOutcome::REMOVED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Transform::map() To map the storage.
     */
    public function map (callable $callback):self {

        return clone($this, [ // @phpstan-ignore assign.propertyType
            'state' => new SharedState(
                Runtime\Arr\Transform::map(
                    $this->state->data(),
                    $callback
                )
            )
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Access::values() To reindex the array.
     * @uses \FireHub\Runtime\Arr\Transform::filter() To filter the storage.
     */
    public function filter (callable $callback):self {

        return clone($this, [ // @phpstan-ignore assign.propertyType
            'state' => new SharedState(
                Runtime\Arr\Access::values(
                    Runtime\Arr\Transform::filter(
                        $this->state->data(),
                        $callback
                    )
                )
            )
        ]);

    }

}