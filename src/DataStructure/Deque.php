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
    Transformation\Filterable, Transformation\Mappable, Transformation\Rejectable,
    Cloneable, Freezable, Thawable
};
use FireHub\Core\Type\Maybe;
use FireHub\Foundation\DataStructure\Boundary\Transformation\ {
    Chunkable, Skippable, Takeable
};
use FireHub\Foundation\DataStructure\Transformation\ {
    Chunk, Select, Skip, Take
};
use FireHub\Foundation\DataStructure\Concern\ {
    Aggregation\CanCount,
    Transformation\CanMultiplicity, Transformation\CanReject
};
use FireHub\Foundation\DataStructure\Stream\Source\FactorySource;
use FireHub\Foundation\State\HasFreezeState;
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
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Rejectable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable<int, TValue>
 *
 * @phpstan-type StorageType = (
 *     Storage<int, TValue>
 *     &Cloneable
 *     &Metrics
 *     &BoundaryAccess<TValue>
 *     &DequeMutation<TValue>
 * )
 */
class Deque implements DequeBoundary, Arrayable, Cloneable, Freezable, Thawable, DequeMutation, Mappable, Rejectable,
    Chunkable, Takeable, Skippable {

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
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Deque::copy() To create a copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Deque::thawState() To thaw the state of the data structure.
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
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function insertFront (mixed ...$values):void {

        $this->guardMutable();

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
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
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
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function insertBack (mixed ...$values):void {

        $this->guardMutable();

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
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
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
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
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
     * Removes and returns the last value from the deque.
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
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
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
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     */
    public function getIterator ():Traversable {

        yield from $this->storage->iterate();

    }

}