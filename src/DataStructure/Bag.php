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
    Cloneable, Forkable
};
use FireHub\Core\Meta\Enum\MutationOutcome;
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
class Bag implements BagBoundary, Arrayable, Cloneable, Forkable, DistinctMetrics, MultiplicityMutation {

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
     */
    public function add (mixed $value, int $count = 1):MutationOutcome {

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
     */
    public function remove (mixed $value, int $count = 1):MutationOutcome {

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
     */
    public function removeAll (mixed $value):MutationOutcome {

        return $this->storage->removeAll($value);

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