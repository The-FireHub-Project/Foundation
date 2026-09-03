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

use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Capability\ {
    DequeMutation, IndexAccess, IndexMutation, LinearBoundaryAccess, Metrics
};
use FireHub\Foundation\Maybe\ {
    None, Some
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
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\LinearBoundaryAccess<TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\DequeMutation<TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\IndexAccess<TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\IndexMutation<TValue>
 */
final class ListStorage implements Storage, Metrics, LinearBoundaryAccess, DequeMutation, IndexAccess, IndexMutation {

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

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     * @uses \FireHub\Runtime\Arr\Access::first() To get the first value.
     *
     * @return \FireHub\Foundation\Maybe\Some<TValue>|\FireHub\Foundation\Maybe\None The first value, or an empty
     * maybe if the storage contains no values.
     */
    public function first ():Some|None {

        if ($this->isEmpty()) return new None();

        /** @var TValue $first */
        $first = Runtime\Arr\Access::first($this->data);

        return new Some($first);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Runtime\Arr\Access::last() To get the last value.
     *
     * @return \FireHub\Foundation\Maybe\Some<TValue>|\FireHub\Foundation\Maybe\None The last value, or an empty
     * maybe if the storage contains no values.
     */
    public function last ():Some|None {

        if ($this->isEmpty()) return new None();

        /** @var TValue $last */
        $last = Runtime\Arr\Access::last($this->data);

        return new Some($last);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Mutation::unshift() To insert values at the beginning of the storage.
     */
    public function insertFront (mixed ...$values):void {

        Runtime\Arr\Mutation::unshift($this->data, ...$values);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Mutation::push() To insert values at the end of the storage.
     */
    public function insertBack (mixed ...$values):void {

        Runtime\Arr\Mutation::push($this->data, ...$values);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Runtime\Arr\Mutation::shift() To remove the first value from the storage.
     *
     * @return \FireHub\Foundation\Maybe\Some<TValue>|\FireHub\Foundation\Maybe\None The removed value, or an empty
     * Maybe if the storage is empty.
     */
    public function removeFront ():Some|None {

        if ($this->isEmpty()) return new None();

        /** @var TValue $shift */
        $shift = Runtime\Arr\Mutation::shift($this->data);

        return new Some($shift);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Runtime\Arr\Mutation::pop() To remove the last value from the storage.
     *
     * @return \FireHub\Foundation\Maybe\Some<TValue>|\FireHub\Foundation\Maybe\None The removed value, or an empty
     * Maybe if the storage is empty.
     */
    public function removeBack ():Some|None {

        if ($this->isEmpty()) return new None();

        /** @var TValue $pop */
        $pop = Runtime\Arr\Mutation::pop($this->data);

        return new Some($pop);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::keyExists() To check if the storage has a value at the specified index.
     */
    public function has (int $index):bool {

        return Runtime\Arr\Access::keyExists($this->data, $index);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::has() To check if the storage has a value at
     * the specified index.
     *
     * @return \FireHub\Foundation\Maybe\Some<TValue>|\FireHub\Foundation\Maybe\None The value at the specified index,
     * or an empty Maybe if the index does not exist.
     */
    public function get (int $index):Some|None {

        if ($this->has($index))
            return new Some($this->data[$index]); // @phpstan-ignore offsetAccess.notFound

        return new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::has() To check if the storage has a value at
     * the specified index.
     */
    public function set (int $index, mixed $value):MutationOutcome {

        $outcome = $this->has($index)
            ? MutationOutcome::UPDATED
            : MutationOutcome::CREATED;

        $this->data[$index] = $value;

        return $outcome;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\ListStorage::has() To check if the storage has a value at
     * the specified index.
     */
    public function remove (int $index):MutationOutcome {

        if (!$this->has($index))
            return MutationOutcome::NOT_FOUND;

        unset($this->data[$index]);

        return MutationOutcome::REMOVED;

    }

}