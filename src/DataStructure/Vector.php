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

use FireHub\Core\Boundary\Type\DataStructure\Collection\Vector as VectorBoundary;
use FireHub\Core\Boundary\Capability\ {
    Access\BoundaryAccess, Access\IndexAccess,
    Conversion\Arrayable,
    Measurement\Metrics,
    Mutation\DequeMutation, Mutation\IndexMutation,
    Transformation\Filterable, Transformation\Mappable,
    Cloneable, Forkable, Freezable, Thawable
};
use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\State\HasFreezeState;
use FireHub\Runtime;
use Traversable;

/**
 * ### Vector data structure
 *
 * Represents a linear, index-addressable collection of values with deterministic ordering and direct access to
 * individual values by their logical position.
 *
 * Vector provides the Foundation implementation of the Core vector contract and serves as a general-purpose
 * contiguous-style sequence abstraction within the FireHub data structure ecosystem.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Collection\Vector<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\DequeMutation<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\IndexMutation<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Filterable<int, TValue>
 *
 * @phpstan-type StorageType = (
 *     Storage<int, TValue>
 *     &Cloneable
 *     &Forkable
 *     &Metrics
 *     &BoundaryAccess<TValue>
 *     &IndexAccess<TValue>
 *     &DequeMutation<TValue>
 *     &IndexMutation<TValue>
 * )
 */
class Vector implements VectorBoundary, Arrayable, Cloneable, Forkable, Freezable, Thawable, DequeMutation,
    IndexMutation, Mappable, Filterable {

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
        protected Storage&Cloneable&Forkable&Metrics&BoundaryAccess&IndexAccess&DequeMutation&IndexMutation $storage
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->toArray();
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
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->copy();
     *
     * // [1, 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::copy() To create a copy of the storage.
     */
    public function copy ():static {

        return clone($this, [
            'storage' => $this->storage->copy()
        ]);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->fork();
     *
     * // [1, 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::fork() To create a fork of the storage.
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
     * @uses \FireHub\Foundation\DataStructure\Vector::fork() To create a fork of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Vector::thawState() To freeze the state of the data structure.
     */
    public function thaw ():static {

        $instance = $this->fork();
        $instance->thawState();

        return $instance;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->isEmpty();
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
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->size();
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
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->first();
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
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->last();
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
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->has(0);
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::has() To check if the storage has a value at the specified
     * index.
     */
    public function has (int $index):bool {

        return $this->storage->has($index);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->get(0);
     *
     * // Maybe(1)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::get() To get the value at the specified index.
     */
    public function get (int $index):Maybe {

        return $this->storage->get($index);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->insertFront('x', 'y', 'z');
     *
     * // ['x', 'y', 'z', 1, 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\FrontMutation::insertFront() To insert values at the front
     * of the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function insertFront (mixed ...$values):void {

        $this->guardMutable();

        $this->storage->insertFront(...$values);

    }

    /**
     * ### Prepends values to the vector
     *
     * Inserts one or more values at the beginning of the vector while preserving their provided order.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->prepend('x', 'y', 'z');
     *
     * // ['x', 'y', 'z', 1, 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Vector::insertFront() To insert values at the front of the vector.
     *
     * @param TValue ...$values <p>
     * Values to prepend to the vector.
     * </p>
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return $this The current vector instance.
     */
    public function prepend (mixed ...$values):static {

        $this->insertFront(...$values);

        return $this;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->insertBack('x', 'y', 'z');
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
     * ### Appends values to the vector
     *
     * Inserts one or more values at the end of the vector while preserving their provided order.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->append('x', 'y', 'z');
     *
     * // [1, 2, 3, 'x', 'y', 'z']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Vector::insertBack() To insert values at the back of the vector.
     *
     * @param TValue ...$values <p>
     * Values to append to the vector.
     * </p>
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return $this The current vector instance.
     */
    public function append (mixed ...$values):static {

        $this->insertBack(...$values);

        return $this;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->removeFront();
     *
     * // Maybe(1))
     *
     * $vector->toArray();
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
     * ### Removes the first value
     *
     * Removes and returns the first value from the vector.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->shift();
     *
     * // Maybe(1))
     *
     * $vector->toArray();
     *
     * // [2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Vector::removeFront() To remove the first value from the vector.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The removed value, or none if the vector is empty.
     */
    public function shift ():Maybe {

        return $this->removeFront();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->removeBack();
     *
     * // Maybe(3))
     *
     * $vector->toArray();
     *
     * // [1, 2]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackMutation::removeBack() To remove the last value from
     * the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function removeBack ():Maybe {

        $this->guardMutable();

        return $this->storage->removeBack();

    }

    /**
     * ### Removes the last value
     *
     * Removes and returns the last value from the vector.
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->pop();
     *
     * // Maybe(3))
     *
     * $vector->toArray();
     *
     * // [1, 2]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Vector::removeBack() To remove the last value from the vector.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     *
     * @return \FireHub\Core\Type\Maybe<TValue|mixed> The removed value, or none if the vector is empty.
     */
    public function pop ():Maybe {

        return $this->removeBack();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->set(0, 'x')
     *
     * // MutationOutcome::UPDATED
     *
     * $vector->toArray();
     *
     * // ['x', 2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\IndexMutation::set() To replace a value in the storage at
     * the specified index.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function set (int $index, mixed $value):MutationOutcome {

        $this->guardMutable();

        return $this->storage->set($index, $value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Vector;
     * use FireHub\Foundation\DataStructure\Storage\ListStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $vector = new Vector(new ListStorage(new ArrayInit([1, 2, 3])));
     *
     * $vector->remove(0);
     *
     * // MutationOutcome::REMOVED
     *
     * $vector->toArray();
     *
     * // [2, 3]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\IndexMutation::remove() To remove a value from the storage
     * at the specified index.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function remove (int $index):MutationOutcome {

        $this->guardMutable();

        return $this->storage->remove($index);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::insertBack() To insert values at the back of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::map() To map the values in the storage using the provided
     * callback.
     */
    public function map (callable $callback):static {

        if ($this->storage instanceOf Mappable)
            return new static($this->storage->map($callback)); // @phpstan-ignore argument.type

        $storage = $this->storage->emptyCopy();
        foreach ($this->storage->iterate() as $index => $value)
            $storage->insertBack($callback($value, $index));

        return new static($storage);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::insertBack() To insert values at the back of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::filter() To filter the values in the storage using the provided
     * callback.
     */
    public function filter (callable $callback):static {

        if ($this->storage instanceof Filterable)
            return new static($this->storage->filter($callback)); // @phpstan-ignore argument.type

        $storage = $this->storage->emptyCopy();
        foreach ($this->storage->iterate() as $index => $value)
            if ($callback($value, $index))
                $storage->insertBack($value);

        return new static($storage);

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