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

use FireHub\Core\Boundary\Type\DataStructure\Collection\Deque as DequeBoundary;
use FireHub\Core\Boundary\Capability\ {
    Access\BoundaryAccess,
    Conversion\Arrayable,
    Measurement\Metrics,
    Mutation\DequeMutation,
    Cloneable
};
use FireHub\Core\Type\Maybe;
use FireHub\Runtime;
use Traversable;

/**
 * ### Deque data structure
 *
 * Represents a linear, double-ended collection of values with deterministic ordering and efficient insertion and
 * removal at both the front and back of the collection.
 *
 * Deque provides the Foundation implementation of the Core deque contract and serves as a general-purpose
 * double-ended sequence abstraction within the FireHub data structure ecosystem.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Collection\Deque<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\DequeMutation<TValue>
 *
 * @phpstan-type StorageType = (Storage<int, TValue>&Cloneable&Metrics&BoundaryAccess<TValue>&DequeMutation<TValue>)
 */
class Deque implements DequeBoundary, Arrayable, Cloneable, DequeMutation {

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
        protected Storage&Cloneable&Metrics&BoundaryAccess&DequeMutation $storage
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->toArray();
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
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->copy();
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
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->isEmpty();
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
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->size();
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
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->first();
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
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->last();
     *
     * // Maybe(3)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::last() To get the last value in the storage.
     */
    public function last ():Maybe {

        return $this->storage->last();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->insertFront('x', 'y', 'z');
     *
     * // ['x', 'y', 'z', 1, 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\FrontMutation::insertFront() To insert values at the front
     * of the storage.
     */
    public function insertFront (mixed ...$values):void {

        $this->storage->insertFront(...$values);

    }

    /**
     * ### Prepends values to the deque
     *
     * Inserts one or more values at the beginning of the deque while preserving their provided order.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->prepend('x', 'y', 'z');
     *
     * // ['x', 'y', 'z', 1, 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Deque::insertFront() To insert values at the front of the deque.
     *
     * @param TValue ...$values <p>
     * Values to prepend to the deque.
     * </p>
     *
     * @return $this The current deque instance.
     */
    public function prepend (mixed ...$values):static {

        $this->insertFront(...$values);

        return $this;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->insertBack('x', 'y', 'z');
     *
     * // [1, 2, 3, 'x', 'y', 'z']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\FrontMutation::insertBack() To insert values at the back
     * of the storage.
     */
    public function insertBack (mixed ...$values):void {

        $this->storage->insertBack(...$values);

    }

    /**
     * ### Appends values to the deque
     *
     * Inserts one or more values at the end of the deque while preserving their provided order.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->append('x', 'y', 'z');
     *
     * // [1, 2, 3, 'x', 'y', 'z']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Deque::insertBack() To insert values at the back of the deque.
     *
     * @param TValue ...$values <p>
     * Values to append to the deque.
     * </p>
     *
     * @return $this The current deque instance.
     */
    public function append (mixed ...$values):static {

        $this->insertBack(...$values);

        return $this;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->removeFront();
     *
     * // Maybe(1))
     *
     * $deque->toArray();
     *
     * // [2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\FrontMutation::removeFront() To remove the first value from
     * the storage.
     */
    public function removeFront ():Maybe {

        return $this->storage->removeFront();

    }

    /**
     * ### Removes the first value
     *
     * Removes and returns the first value from the deque.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->shift();
     *
     * // Maybe(1))
     *
     * $deque->toArray();
     *
     * // [2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Deque::removeFront() To remove the first value from the deque.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The removed value, or none if the deque is empty.
     */
    public function shift ():Maybe {

        return $this->removeFront();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->removeBack();
     *
     * // Maybe(3))
     *
     * $deque->toArray();
     *
     * // [1, 2]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackMutation::removeBack() To remove the last value from
     * the storage.
     */
    public function removeBack ():Maybe {

        return $this->storage->removeBack();

    }

    /**
     * ### Removes the last value
     *
     * Removes and returns the last value from the dwque.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Deque;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $deque = new Deque(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $deque->pop();
     *
     * // Maybe(3))
     *
     * $deque->toArray();
     *
     * // [1, 2]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Deque::removeBack() To remove the last value from the deque.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The removed value, or none if the deque is empty.
     */
    public function pop ():Maybe {

        return $this->removeBack();

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