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

use FireHub\Core\Boundary\Type\DataStructure\Collection\Bag as BagBoundary;
use FireHub\Core\Boundary\Capability\ {
    Access\MultiplicityAccess,
    Conversion\Arrayable,
    Measurement\DistinctMetrics,
    Mutation\MultiplicityMutation,
    Transformation\Filterable, Transformation\Mappable, Transformation\Rejectable,
    Cloneable, Forkable, Freezable, Thawable
};
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Concern\Transformation\CanReject;
use FireHub\Foundation\State\HasFreezeState;
use FireHub\Runtime;
use Traversable;

/**
 * ### Bag data structure
 *
 * Represents an unordered collection of values where logically equal values may occur multiple times.
 *
 * A Bag preserves the multiplicity of its values without assigning positional or user-defined key semantics to
 * individual occurrences. Each distinct value may therefore be represented by one or more occurrences.
 *
 * The Bag delegates storage, equality, multiplicity tracking, and mutation behavior to the configured storage
 * implementation while exposing the public Bag semantics defined by the Core boundary.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Collection\Bag<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\MultiplicityMutation<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Rejectable<int, TValue>
 *
 * @phpstan-type StorageType = (
 *     Storage<int, TValue>
 *     &Cloneable
 *     &Forkable
 *     &DistinctMetrics
 *     &MultiplicityAccess<TValue>
 *     &MultiplicityMutation<TValue>
 * )
 */
class Bag implements BagBoundary, Arrayable, Cloneable, Forkable, Freezable, Thawable, DistinctMetrics,
    MultiplicityMutation, Mappable, Rejectable {

    /**
     * ### Freeze state
     * @since 1.0.0
     */
    use HasFreezeState;

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
        protected Storage&Cloneable&Forkable&DistinctMetrics&MultiplicityAccess&MultiplicityMutation $storage
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $bag->toArray();
     *
     * // ['John', 'John', 'John', 'Jane', 'Jane', 'Richard']
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
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $bag->copy();
     *
     * // ['John', 'John', 'John', 'Jane', 'Jane', 'Richard']
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
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $bag->fork();
     *
     * // ['John', 'John', 'John', 'Jane', 'Jane', 'Richard']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::fork() To create a fork of the storage.
     */
    public function fork ():static {

        return new static($this->storage->fork());

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Bag::fork() To create a fork of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Bag::thawState() To thaw the state of the data structure.
     */
    public function thaw ():static {

        $instance = $this->fork();
        $instance->thawState();

        return $instance;

    }

    /**
     * {@inheritDoc}
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
     *
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $bag->size();
     *
     * // 6
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
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $bag->distinctSize();
     *
     * // 3
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::distinctSize() To get the distinct size of the storage.
     */
    public function distinctSize ():int {

        return $this->storage->distinctSize();

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $set->contains('John');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::contains() To check if the storage contains the specified value.
     */
    public function contains (mixed $value):bool {

        return $this->storage->contains($value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $set->frequency('John');
     *
     * // 3
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::frequency() To get the frequency of the specified value.
     */
    public function frequency (mixed $value):int {

        return $this->storage->frequency($value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $bag->add('John')
     *
     * // MutationOutcome::UPDATED
     *
     * $bag->add('Jana')
     *
     * // MutationOutcome::CREATED
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add the specified value.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function add (mixed $value, int $count = 1):MutationOutcome {

        $this->guardMutable();

        return $this->storage->add($value, $count);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $bag->remove('Richard')
     *
     * // MutationOutcome::REMOVED
     *
     * $bag->remove('John')
     *
     * // MutationOutcome::UPDATED
     *
     * $bag->remove('Jana')
     *
     * // MutationOutcome::NOT_FOUND
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::remove() To remove the specified value.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function remove (mixed $value, int $count = 1):MutationOutcome {

        $this->guardMutable();

        return $this->storage->remove($value, $count);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $bag = new HashBagStorage(new StringHashStrategy);
     *
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('John');
     * $bag->add('Jane');
     * $bag->add('Jane');
     * $bag->add('Richard');
     *
     * $bag->removeAll('John')
     *
     * // MutationOutcome::REMOVED
     *
     * $bag->removeAll('Jana')
     *
     * // MutationOutcome::NOT_FOUND
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::removeAll() To remove all occurrences of the specified value.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function removeAll (mixed $value):MutationOutcome {

        $this->guardMutable();

        return $this->storage->removeAll($value);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add mapped values into the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::map() To map the values in the storage using the provided
     * callback.
     */
    public function map (callable $callback):static {

        if ($this->storage instanceof Mappable)
            return new static($this->storage->map($callback)); // @phpstan-ignore argument.type

        $storage = $this->storage->emptyCopy();
        foreach ($this->storage->iterate() as $key => $value)
            $storage->add($callback($value, $key));

        return new static($storage);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add mapped values into the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::filter() To filter the values in the storage using the provided
     * callback.
     */
    public function filter (callable $callback):static {

        if ($this->storage instanceof Filterable)
            return new static($this->storage->filter($callback)); // @phpstan-ignore argument.type

        $storage = $this->storage->emptyCopy();
        foreach ($this->storage->iterate() as $key => $value)
            if ($callback($value, $key))
                $storage->add($key); // @phpstan-ignore argument.type

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