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

use FireHub\Core\Boundary\Type\DataStructure\Collection\PriorityQueue as PriorityQueueBoundary;
use FireHub\Core\Boundary\Capability\ {
    Conversion\Arrayable,
    Mutation\FrontRemoval,
    Cloneable, Forkable, Freezable, Thawable
};
use FireHub\Core\Type\Maybe;
use FireHub\Core\Type\DataStructure\PriorityQueue\Entry;
use FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage;
use FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit;
use FireHub\Foundation\State\HasFreezeState;
use FireHub\Runtime;
use Closure, Traversable;

/**
 * ### Priority queue data structure
 *
 * Represents a priority-ordered collection in which each value is associated with an explicit priority.
 *
 * The configured comparator determines the ordering of priorities. A negative comparison result indicates that the
 * priority should be processed before the second priority.
 *
 * Entries with equivalent priorities are processed in insertion order, providing stable priority ordering.
 *
 * The priority queue uses binary heap storage to maintain its ordering invariant after every insertion and removal.
 * Iteration follows the internal heap representation and does not guarantee globally sorted order.
 * @since 1.0.0
 *
 * @template TPriority
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Collection\PriorityQueue<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, StorageEntry>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\FrontRemoval<TValue>
 *
 * @phpstan-type StorageEntry array{
 *     entry: \FireHub\Core\Type\DataStructure\PriorityQueue\Entry<TPriority, TValue>,
 *     sequence: non-negative-int
 * }
 */
class PriorityQueue implements PriorityQueueBoundary, Arrayable, Cloneable, Forkable, Freezable, Thawable, FrontRemoval {

    /**
     * ### Freeze state
     * @since 1.0.0
     */
    use HasFreezeState;

    /**
     * ### Binary heap storage
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage<StorageEntry>
     */
    protected BinaryHeapStorage $storage;

    /**
     * ### Next insertion sequence
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    protected int $sequence = 0;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param Closure(TPriority, TPriority):int $comparator <p>
     * Comparator used to determine priority ordering.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected readonly Closure $comparator
    ) {

        /** @var \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage<StorageEntry> $storage */
        $storage = new BinaryHeapStorage(
            function (mixed $left, mixed $right):int {

                $comparison = ($this->comparator)(
                    $left['entry']->priority, // @phpstan-ignore-line
                    $right['entry']->priority // @phpstan-ignore-line
                );

                return $comparison !== 0
                    ? $comparison
                    : $left['sequence'] <=> $right['sequence']; // @phpstan-ignore-line

            },
            new EmptyInit
        );

        $this->storage = $storage;

    }

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
     * ### Peeks at the first value in the queue
     *
     * Returns the first value in the heap without removing it.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\PriorityQueue::first() To get the first value.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The last value in the queue, wrapped in a Maybe.
     */
    public function peek ():Maybe {

        return $this->first();

    }

    /**
     * ### Enqueues a value
     *
     * Inserts a value with the specified priority.
     *
     * Values with equivalent priorities are processed in insertion order.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::insert() To insert values into the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     * @uses \FireHub\Core\Type\DataStructure\PriorityQueue\Entry To create a new entry.
     *
     * @param TValue $value <p>
     * Value to enqueue.
     * </p>
     * @param TPriority $priority <p>
     * Priority associated with the value.
     * </p>
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return void
     */
    public function enqueue (mixed $value, mixed $priority):void {

        $this->guardMutable();

        $this->storage->insert([
            'entry' => new Entry($priority, $value),
            'sequence' => $this->sequence++
        ]);

    }

    /**
     * ### Enqueues an entry
     *
     * Inserts an existing priority queue entry.
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\DataStructure\PriorityQueue\Entry<TPriority, TValue> $entry <p>
     * Entry to enqueue.
     * </p>
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return void
     */
    public function enqueueEntry (Entry $entry):void {

        $this->guardMutable();

        $this->storage->insert([
            'entry' => $entry,
            'sequence' => $this->sequence++
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\FrontMutation::removeFront() To remove the first value from
     * the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function removeFront ():Maybe {

        $this->guardMutable();

        return $this->storage->removeFront();

    }

    /**
     * ### Dequeues the highest-priority value
     *
     * Removes and returns the highest-priority value.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\PriorityQueue::removeFront() To remove the first value from the priority
     * queue.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The removed value, wrapped in a Maybe.
     */
    public function dequeue ():Maybe {

        $this->guardMutable();

        return $this->storage->removeFront();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     */
    public function getIterator ():Traversable {

        $index = 0;

        foreach ($this->storage->iterate() as $item)
            yield $index++ => $item['entry']->value;

    }

    /**
     * ### Iterates over priority queue entries
     *
     * Iteration follows an internal heap order rather than a globally sorted priority order.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     *
     * @return Traversable<int, \FireHub\Core\Type\DataStructure\PriorityQueue\Entry<TPriority, TValue>>
     * Priority queue entries.
     */
    public function entries ():Traversable {

        $index = 0;

        foreach ($this->storage->iterate() as $item)
            yield $index++ => $item['entry'];

    }

}