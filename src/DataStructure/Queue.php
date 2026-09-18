<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure;

use FireHub\Core\Boundary\Type\DataStructure\Collection\Queue as QueueBoundary;
use FireHub\Core\Boundary\Capability\ {
    Access\FrontAccess,
    Conversion\Arrayable,
    Measurement\Metrics,
    Mutation\BackInsertion, Mutation\FrontRemoval,
    Cloneable, Freezable, Thawable
};
use FireHub\Core\Type\Maybe;
use FireHub\Foundation\State\HasFreezeState;
use FireHub\Runtime;
use Traversable;

/**
 * ### Queue data structure
 *
 * Represents a linear, first-in, first-out collection of values with deterministic ordering and access to the
 * earliest added value at the front of the collection.
 *
 * Queue provides the Foundation implementation of the Core queue contract and serves as a general-purpose FIFO
 * sequence abstraction within the FireHub data structure ecosystem.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Collection\Queue<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\BackInsertion<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\FrontRemoval<TValue>
 *
 * @phpstan-type StorageType = (
 *     Storage<int, TValue>
 *     &Cloneable
 *     &Metrics
 *     &FrontAccess<TValue>
 *     &BackInsertion<TValue>
 *     &FrontRemoval<TValue>
 * )
 */
class Queue implements QueueBoundary, Arrayable, Cloneable, Freezable, Thawable, BackInsertion, FrontRemoval {

    /**
     * ### Freeze state
     * @since 1.0.0
     */
    use HasFreezeState;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param StorageType $storage <p>
     * The storage to use for the data structure.
     * </p>
     *
     * @return void
     */
    final public function __construct (
        protected Storage&Cloneable&Metrics&FrontAccess&BackInsertion&FrontRemoval $storage
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->toArray();
     *
     * // [1, 2, 3]
     * </code>
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
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->copy();
     *
     * // [1, 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::copy() To create a copy of the storage.
     */
    public function copy ():static {

        return new static($this->storage->copy());

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Queue::copy() To create a copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Queue::thawState() To freeze the state of the data structure.
     */
    public function thaw ():static {

        $instance = $this->copy();
        $instance->thawState();

        return $instance;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->isEmpty();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::isEmpty() To check if the storage is empty.
     */
    public function isEmpty ():bool {

        return $this->storage->isEmpty();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->size();
     *
     * // 3
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::size() To get the size of the storage.
     */
    public function size ():int {

        return $this->storage->size();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->first();
     *
     * // Maybe(1)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::first() To get the first value in the storage.
     */
    public function first ():Maybe {

        return $this->storage->first();

    }

    /**
     * ### Peeks at the last value in the queue
     *
     * Returns the last value in the queue without removing it.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->peek();
     *
     * // Maybe(1)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Queue::first() To get the first value in the storage.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The first value in the queue, wrapped in a Maybe.
     */
    public function peek ():Maybe {

        return $this->first();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->insertBack('x', 'y', 'z');
     *
     * // [1, 2, 3, 'x', 'y', 'z']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\FrontMutation::insertBack() To insert values at the back
     * of the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function insertBack (mixed ...$values):void {

        $this->guardMutable();

        $this->storage->insertBack(...$values);

    }

    /**
     * ### Enqueues values to the queue
     *
     * Inserts one or more values at the back of the queue while preserving their provided order.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->enqueue('x', 'y', 'z');
     *
     * // [1, 2, 3, 'x', 'y', 'z']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Queue::insertBack() To insert values at the back of the deque.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function enqueue (mixed ...$values):void {

        $this->insertBack(...$values);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->removeFront();
     *
     * // Maybe(1))
     *
     * $queue->toArray();
     *
     * // [2, 3]
     * </code>
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
     * ### Removes the first value from the queue
     *
     * Removes and returns the first value from the queue.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Queue;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $queue = new Queue(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $queue->dequeue();
     *
     * // Maybe(1))
     *
     * $queue->toArray();
     *
     * // [2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Queue::removeFront() To remove the first value from the queue.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The removed value, wrapped in a Maybe.
     */
    public function dequeue ():Maybe {

        return $this->removeFront();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     */
    public function getIterator ():Traversable {

        yield from $this->storage->iterate();

    }

}