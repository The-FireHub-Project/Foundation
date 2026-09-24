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
    Transformation\Filterable, Transformation\Mappable, Transformation\Rejectable,
    Cloneable, Forkable, Freezable, Thawable
};
use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\ {
    Side, MutationOutcome
};
use FireHub\Foundation\DataStructure\Storage\HashStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash;
use FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit;
use FireHub\Foundation\DataStructure\Boundary\Transformation\ {
    Chunkable, Groupable, Padable, Reversible, Shufflable, Skippable, Sliceable, Spliceable, Splittable, Takeable
};
use FireHub\Foundation\DataStructure\Transformation\ {
    Chunk, Select, Skip, Split, Take
};
use FireHub\Foundation\DataStructure\Concern\ {
    Aggregation\CanCount,
    Transformation\CanMultiplicity, Transformation\CanReject
};
use FireHub\Foundation\DataStructure\Stream\Source\FactorySource;
use FireHub\Foundation\State\HasFreezeState;
use FireHub\Foundation\DataStructure\Exception\InvalidRangeLength;
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
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Rejectable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Splittable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Groupable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Sliceable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Spliceable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Reversible<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Shufflable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Padable<int, TValue>
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
    IndexMutation, Mappable, Rejectable, Chunkable, Splittable, Groupable, Takeable, Skippable, Sliceable, Spliceable,
    Reversible, Shufflable, Padable {

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
     * ### Provides multiplicity capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Transformation\CanMultiplicity<int, TValue>
     */
    use CanMultiplicity;

    /**
     * ### Provides rejection capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Transformation\CanReject<int, TValue>
     */
    use CanReject;

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
     * @uses \FireHub\Foundation\DataStructure\Vector::thawState() To thaw the state of the data structure.
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
     * ### Creates a Select instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Select<int, TValue, $this> A select transformation of
     * the data structure.
     */
    public function select ():Select {

        /** @var Select<int, TValue, $this> */
        return new Select($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\FactorySource To create a stream source from a factory
     * function.
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::insertBack() To insert values at the storage.
     */
    public function chunkBy (callable $callback):Stream {

        return new Stream(
            new FactorySource(
                function () use ($callback):iterable {

                    $storage = $this->storage->emptyCopy();
                    $has_values = false;

                    foreach ($this->storage->iterate() as $index => $value) {

                        if ($has_values && $callback($value, $index)) {

                            yield new static($storage);

                            $storage = $this->storage->emptyCopy();

                        }

                        $storage->insertBack($value);
                        $has_values = true;

                    }

                    if ($has_values)
                        yield new static($storage);

                }
            )
        );

    }

    /**
     * ### Creates a Chunk instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Chunk<int, TValue, $this> A chunk of the data structure.
     */
    public function chunk ():Chunk {

        /** @var Chunk<int, TValue, $this> */
        return new Chunk($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\FactorySource To create a stream source from a factory
     * function.
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::insertBack() To insert values at the storage.
     */
    public function splitBy (callable $callback):Stream {

        return new Stream(
            new FactorySource(
                function () use ($callback):iterable {

                    $storage = $this->storage->emptyCopy();
                    $has_values = false;

                    foreach ($this->storage->iterate() as $index => $value) {

                        if ($callback($value, $index)) {

                            if ($has_values)
                                yield new static($storage);

                            $storage = $this->storage->emptyCopy();
                            $has_values = false;

                            continue;

                        }

                        $storage->insertBack($value);
                        $has_values = true;

                    }

                    if ($has_values)
                        yield new static($storage);

                }
            )
        );

    }

    /**
     * ### Creates a Split instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Split<int, TValue, $this> A split transformation of the
     * data structure.
     */
    public function split ():Split {

        /** @var Split<int, TValue, $this> */
        return new Split($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty storage for each generated group.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the source storage.
     * @uses \FireHub\Foundation\DataStructure\Map::set() To associate a group with its identity.
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackInsertion::insertBack() To insert values at the back of the
     * storage.
     *
     * @template TGroup of array-key
     */
    public function groupBy (callable $selector):Map {

        /**
         * Native PHP array is intentionally used as the temporary group index.
         *
         * Group values are written directly into their destination storage during
         * source iteration, avoiding repeated Map lookups, Maybe allocation, and
         * high-level Vector mutation in the hot path.
         *
         * @var array<TGroup, StorageType> $groups
         */
        $groups = [];

        foreach ($this->storage->iterate() as $index => $value) {

            $identity = $selector($value, $index);

            if (isset($groups[$identity])) {

                $groups[$identity]->insertBack($value);

                continue;

            }

            $storage = $this->storage->emptyCopy();

            $storage->insertBack($value);

            $groups[$identity] = $storage;

        }

        $result = new Map(new HashStorage(new ArrHash(new EmptyInit)));

        foreach ($groups as $identity => $storage)
            $result->set($identity, new static($storage));

        /** @var \FireHub\Foundation\DataStructure\Map<TGroup, static> */
        return $result;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackInsertion::insertBack() To insert values at the back of the
     * storage.
     */
    public function takeWhile (callable $callback):static {

        $storage = $this->storage->emptyCopy();

        foreach ($this->storage->iterate() as $index => $value) {

            if (!$callback($value, $index))
                break;

            $storage->insertBack($value);

        }

        return new static($storage);

    }

    /**
     * ### Creates a Take instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Take<int, TValue, $this> A take transformation of the
     * data structure.
     */
    public function take ():Take {

        /** @var Take<int, TValue, $this> */
        return new Take($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackInsertion::insertBack() To insert values at the back of the
     * storage.
     */
    public function skipWhile (callable $callback):static {

        $storage = $this->storage->emptyCopy();
        $skipping = true;

        foreach ($this->storage->iterate() as $index => $value) {

            if ($skipping) {

                if ($callback($value, $index))
                    continue;

                $skipping = false;

            }

            $storage->insertBack($value);

        }

        return new static($storage);

    }

    /**
     * ### Creates a Skip instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Skip<int, TValue, $this> A skip transformation of the
     * data structure.
     */
    public function skip ():Skip {

        /** @var Skip<int, TValue, $this> */
        return new Skip($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Sliceable::slice() To slice the source storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the source storage.
     * @uses \FireHub\Core\Boundary\Capability\Measurement\Metrics::size() To get the size of the source storage.
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackInsertion::insertBack() To insert values into the resulting
     * storage.
     * @uses \FireHub\Runtime\Math::min() To clamp the start index to the size of the source storage.
     * @uses \FireHub\Runtime\Math::max() To clamp the end index to the size of the source storage.
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\InvalidRangeLength If the range length is less than zero.
     */
    public function slice (int $offset, ?int $length = null):static {

        if ($length !== null && $length < 0)
            throw new InvalidRangeLength(
                'Range length must be greater than or equal to zero.'
            );

        if ($this->storage instanceof Sliceable)
            return new static(
                $this->storage->slice($offset, $length)
            );

        $size = $this->storage->size();

        $start = $offset >= 0
            ? Runtime\Math::min($offset, $size)
            : Runtime\Math::max(0, $size + $offset);

        $end = $length === null
            ? $size
            : Runtime\Math::min($size, $start + max(0, $length));

        $storage = $this->storage->emptyCopy();

        $position = 0;
        foreach ($this->storage->iterate() as $value) {

            if ($position >= $end)
                break;

            if ($position >= $start)
                $storage->insertBack($value);

            $position++;

        }

        return new static($storage);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Spliceable::splice() To use an optimized storage
     * splicing implementation when available.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To guard against mutation of a frozen vector.
     * @uses \FireHub\Core\Boundary\Capability\Measurement\Metrics::size() To get the size of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create empty storages.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackInsertion::insertBack() To insert values into storages.
     * @uses \FireHub\Runtime\Math::min() To clamp the start index to the size of the source storage.
     * @uses \FireHub\Runtime\Math::max() To clamp the end index to the size of the source storage.
     *
     * @template TReplacementValue
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     * @throws \FireHub\Foundation\DataStructure\Exception\InvalidRangeLength If the range length is less than zero.
     */
    public function splice (int $offset, ?int $length = null, iterable $replacement = []):static {

        $this->guardMutable();

        if ($length !== null && $length < 0)
            throw new InvalidRangeLength(
                'Range length must be greater than or equal to zero.'
            );

        if ($this->storage instanceof Spliceable)
            return new static(
                $this->storage->splice($offset, $length, $replacement)
            );

        $size = $this->storage->size();

        $start = $offset >= 0
            ? Runtime\Math::min($offset, $size)
            : Runtime\Math::max(0, $size + $offset);

        $end = $length === null
            ? $size
            : Runtime\Math::min($size, $start + $length);

        /** @var StorageType $storage */
        $storage = $this->storage->emptyCopy();
        $removed = $this->storage->emptyCopy();

        $position = 0; $replacement_inserted = false;
        foreach ($this->storage->iterate() as $value) {

            if (!$replacement_inserted && $position === $start) {

                foreach ($replacement as $replacement_value)
                    $storage->insertBack($replacement_value);

                $replacement_inserted = true;

            }

            if ($position >= $start && $position < $end)
                $removed->insertBack($value);
            else
                $storage->insertBack($value);

            $position++;

        }

        if (!$replacement_inserted)
            foreach ($replacement as $replacement_value)
                $storage->insertBack($replacement_value);

        $this->storage = $storage;

        return new static($removed);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::insertBack() To insert values at the back of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::reverse() To reverse the values in the storage.
     * @uses \FireHub\Runtime\Arr\Transform::reverse() To reverse the values in an array.
     * @uses \FireHub\Runtime\Iterator::toArray() To convert an iterator to an array.
     */
    public function reverse ():static {

        if ($this->storage instanceof Reversible)
            return new static($this->storage->reverse());

        $storage = $this->storage->emptyCopy();

        $values = Runtime\Arr\Transform::reverse(
            Runtime\Iterator::toArray(
                $this->storage->iterate()
            )
        );

        $storage->insertBack(...$values);

        return new static($storage);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::insertBack() To insert values into the storage.
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Shufflable::shuffle() To shuffle the underlying
     * storage when supported.
     * @uses \FireHub\Runtime\Arr\Ordering::shuffle() To shuffle the materialized values.
     */
    public function shuffle ():static {

        if ($this->storage instanceof Shufflable)
            return new static($this->storage->shuffle());

        $values = [];

        foreach ($this->storage->iterate() as $value)
            $values[] = $value;

        Runtime\Arr\Ordering::shuffle($values);

        $storage = $this->storage->emptyCopy();

        foreach ($values as $value)
            $storage->insertBack($value);

        return new static($storage);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::insertFront() To insert values into the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::insertBack() To insert values into the storage.
     * @uses \FireHub\Runtime\Math::divideInt() To calculate the padding size.
     */
    public function pad (int $size, mixed $value, Side $side = Side::RIGHT):static {

        if ($this->storage instanceof Padable)
            return new static($this->storage->pad($size, $value, $side));

        $storage = $this->storage->copy();

        $remaining = $size - $storage->size();

        if ($remaining <= 0)
            return new static($storage);

        [$left, $right] = match ($side) {
            Side::LEFT => [$remaining, 0],
            Side::RIGHT => [0, $remaining],
            Side::BOTH => [
                Runtime\Math::divideInt($remaining, 2),
                $remaining - Runtime\Math::divideInt($remaining, 2)
            ]
        };

        while ($left-- > 0)
            $storage->insertFront($value);

        while ($right-- > 0)
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