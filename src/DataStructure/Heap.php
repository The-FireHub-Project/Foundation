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

namespace FireHub\Foundation\DataStructure;

use FireHub\Core\Boundary\Type\DataStructure\Collection\Heap as HeapBoundary;
use FireHub\Core\Boundary\Capability\ {
    Conversion\Arrayable,
    Mutation\FrontRemoval, Mutation\Insertion,
    Cloneable, Forkable, Freezable, Thawable
};
use FireHub\Core\Type\Maybe;
use FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible;
use FireHub\Foundation\DataStructure\Concern\ {
    Aggregation\CanCount, Aggregation\CanReduce,
    Query\CanFind, Query\CanMatch
};
use FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage;
use FireHub\Foundation\State\HasFreezeState;
use FireHub\Runtime;
use Traversable;

/**
 * ### Heap data structure
 *
 * Represents a priority-ordered collection backed by a binary heap.
 *
 * The configured comparator determines which value has the highest priority. A negative comparison result indicates
 * that the first value should appear before the second value.
 *
 * The heap maintains its ordering invariant after every insertion and removal. Iteration follows the internal heap
 * representation and does not guarantee globally sorted order.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Collection\Heap<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\FrontRemoval<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\Insertion<TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible<TValue>
 */
class Heap implements HeapBoundary, Arrayable, Cloneable, Forkable, Freezable, Thawable, FrontRemoval, Insertion,
    Reducible {

    /**
     * ### Freeze state
     * @since 1.0.0
     */
    use HasFreezeState;

    /**
     * ### Provides countable capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Aggregation\CanCount<int, TValue>
     */
    use CanCount;

    /**
     * ### Provides matching query support
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Query\CanMatch<int, TValue>
     */
    use CanMatch;

    /**
     * ### Provides finding query support
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Query\CanFind<int, TValue>
     */
    use CanFind;

    /**
     * ### Provides value reduction
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Aggregation\CanReduce<TValue>
     */
    use CanReduce;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage<TValue> $storage <p>
     * The storage to use for the data structure.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected BinaryHeapStorage $storage
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Iterator::toArray() To convert the storage to an array.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     */
    public function toArray ():array {

        return Runtime\Iterator::toArray($this->storage->iterate());

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::copy() To create a copy of the storage.
     */
    public function copy ():static {

        return clone($this, [
            'storage' => $this->storage->copy()
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::fork() To create a fork of the storage.
     */
    public function fork ():static {

        return clone($this, [
            'storage' => $this->storage->fork()
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Heap::fork() To create a fork of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Heap::thawState() To thaw the state of the data structure.
     */
    public function thaw ():static {

        $instance = $this->fork();
        $instance->thawState();

        return $instance;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::isEmpty() To get the size of the storage.
     */
    public function isEmpty ():bool {

        return $this->storage->isEmpty();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::size() To get the size of the storage.
     */
    public function size ():int {

        return $this->storage->size();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::first() To get the first value.
     */
    public function first ():Maybe {

        return $this->storage->first();

    }

    /**
     * ### Peeks at the first value in the heap
     *
     * Returns the first value in the heap without removing it.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Heap::first() To get the first value.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The last value in the heap, wrapped in a Maybe.
     */
    public function peek ():Maybe {

        return $this->first();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::insert() To insert values into the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function insert (mixed ...$values):void {

        $this->guardMutable();

        $this->storage->insert(...$values);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::removeFront() To remove the first value
     * from the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function removeFront ():Maybe {

        $this->guardMutable();

        return $this->storage->removeFront();

    }

    /**
     * ### Extracts the highest-priority value
     *
     * Removes and returns the highest-priority value from the heap.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Heap::removeFront() To remove the firsr value from the heap.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The removed value, or none if the heap is empty.
     */
    public function extract ():Maybe {

        return $this->removeFront();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::iterate() To iterate over the storage.
     */
    public function getIterator ():Traversable {

        yield from $this->storage->iterate();

    }

}