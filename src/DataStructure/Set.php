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

use FireHub\Core\Boundary\Type\DataStructure\Collection\Set as SetBoundary;
use FireHub\Core\Boundary\Capability\ {
    Access\ValueAccess,
    Conversion\Arrayable,
    Measurement\Metrics,
    Mutation\ValueMutation,
    Query\RandomSelectable,
    Transformation\Filterable, Transformation\Mappable, Transformation\Rejectable,
    Cloneable, Forkable, Freezable, Thawable
};
use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible;
use FireHub\Foundation\DataStructure\Boundary\Algebra\ {
    SetAlgebra, SetRelations
};
use FireHub\Foundation\DataStructure\Boundary\Transformation\Partitionable;
use FireHub\Foundation\DataStructure\Concern\ {
    Aggregation\CanCount, Aggregation\CanReduce,
    Query\CanFind, Query\CanMatch,
    Transformation\CanReject
};
use FireHub\Foundation\State\HasFreezeState;
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Runtime;
use Traversable;

/**
 * ### Set data structure
 *
 * Represents a collection of unique values where each value can occur at most once.
 *
 * Set provides the Foundation implementation of the Core Set contract and serves as a general-purpose collection
 * for value membership and uniqueness.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Collection\Set<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\ValueMutation<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Rejectable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Partitionable<int, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible<TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Algebra\SetAlgebra<TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Algebra\SetRelations<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Query\RandomSelectable<TValue>
 *
 * @phpstan-type StorageType = (
 *     Storage<int, TValue>
 *     &Cloneable
 *     &Forkable
 *     &Metrics
 *     &ValueAccess<TValue>
 *     &ValueMutation<TValue>
 * )
 */
class Set implements SetBoundary, Arrayable, Cloneable, Forkable, Freezable, Thawable, ValueMutation, Mappable,
    Rejectable, Partitionable, SetAlgebra, SetRelations, Reducible, RandomSelectable {

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
     * ### Provides rejection capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Transformation\CanReject<int, TValue>
     */
    use CanReject;

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
     * @param StorageType $storage <p>
     * The storage to use for the data structure.
     * </p>
     *
     * @return void
     */
    final public function __construct (
        protected Storage&Cloneable&Forkable&Metrics&ValueAccess&ValueMutation $storage
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $set = new Set(new HashSetStorage(new StringHashStrategy));
     *
     * $set->add('John');
     * $set->add('Jane');
     * $set->add('Richard');
     *
     * $set->toArray();
     *
     * // ['John', 'Jane', 'Richard']
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
     * use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $set = new Set(new HashSetStorage(new StringHashStrategy));
     *
     * $set->add('John');
     * $set->add('Jane');
     * $set->add('Richard');
     *
     * $set->copy();
     *
     * // ['John', 'Jane', 'Richard']
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
     * use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $set = new Set(new HashSetStorage(new StringHashStrategy));
     *
     * $set->add('John');
     * $set->add('Jane');
     * $set->add('Richard');
     *
     * $set->fork();
     *
     * // ['John', 'Jane', 'Richard']
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
     * @uses \FireHub\Foundation\DataStructure\Set::fork() To create a fork of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::thawState() To thaw the state of the data structure.
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
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $set = new Set(new HashSetStorage(new StringHashStrategy));
     *
     * $set->add('John');
     * $set->add('Jane');
     * $set->add('Richard');
     *
     * $set->size();
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
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $set = new Set(new HashSetStorage(new StringHashStrategy));
     *
     * $set->add('John');
     * $set->add('Jane');
     * $set->add('Richard');
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
     * use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $set = new Set(new HashSetStorage(new StringHashStrategy));
     *
     * $set->add('John');
     * $set->add('Jane');
     * $set->add('Richard');
     *
     * $set->add('John')
     *
     * // MutationOutcome::ALREADY_EXISTS
     *
     *  $set->add('Jana')
     *
     * // MutationOutcome::CREATED
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\ValueMutation::add() To add a value to the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function add (mixed $value):MutationOutcome {

        $this->guardMutable();

        return $this->storage->add($value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Set;
     * use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
     * use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\StringHashStrategy;
     *
     * $set = new Set(new HashSetStorage(new StringHashStrategy));
     *
     * $set->add('John');
     * $set->add('Jane');
     * $set->add('Richard');
     *
     * $set->remove('John')
     *
     * // MutationOutcome::REMOVED
     *
     *  $set->remove('Jana')
     *
     * // MutationOutcome::NOT_FOUND
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\ValueMutation::remove() To remove a value from the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function remove (mixed $value):MutationOutcome {

        $this->guardMutable();

        return $this->storage->remove($value);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add values into the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check if the data structure contains.
     * a value.
     */
    public function isSubsetOf (iterable $values):bool {

        $other = $this->storage->emptyCopy();

        foreach ($values as $value)
            $other->add($value);

        foreach ($this as $value)
            if (!$other->contains($value))
                return false;

        return true;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Set::size() To get the size of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add values into the data structure.
     * @uses \FireHub\Runtime\FileSystem\Storage::size() To get the size of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check if the data structure contains.
     * a value.
     */
    public function isProperSubsetOf (iterable $values):bool {

        $other = $this->storage->emptyCopy();

        foreach ($values as $value)
            $other->add($value);

        if ($this->size() >= $other->size())
            return false;

        foreach ($this as $value)
            if (!$other->contains($value))
                return false;

        return true;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add values into the data structure.
     * @uses \FireHub\Runtime\FileSystem\Storage::iterate() To iterate over the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check if the data structure contains.
     * a value.
     */
    public function isSupersetOf (iterable $values):bool {

        $other = $this->storage->emptyCopy();

        foreach ($values as $value)
            $other->add($value);

        foreach ($other->iterate() as $value)
            if (!$this->contains($value))
                return false;

        return true;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add values into the data structure.
     * @uses \FireHub\Runtime\FileSystem\Storage::iterate() To iterate over the data structure.
     * @uses \FireHub\Runtime\FileSystem\Storage::size() To get the size of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::size() To get the size of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check if the data structure contains.
     * a value.
     */
    public function isProperSupersetOf (iterable $values):bool {

        $other = $this->storage->emptyCopy();

        foreach ($values as $value)
            $other->add($value);

        if ($this->size() <= $other->size())
            return false;

        foreach ($other->iterate() as $value)
            if (!$this->contains($value))
                return false;

        return true;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check if the data structure contains a value.
     */
    public function isDisjointWith (iterable $values):bool {

        foreach ($values as $value)
            if ($this->contains($value))
                return false;

        return true;

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
                $storage->add($value); // @phpstan-ignore argument.type

        return new static($storage);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create empty storages for the generated partitions.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the source storage.
     * @uses \FireHub\Foundation\DataStructure\Set::add() To add values into their corresponding partition.
     */
    public function partition (callable $callback):Tuple {

        $matched = $this->storage->emptyCopy();
        $unmatched = $this->storage->emptyCopy();

        foreach ($this->storage->iterate() as $index => $value) {

            if ($callback($value, $index))
                $matched->add($value);
            else
                $unmatched->add($value);

        }

        return DS::tuple()->arr([ // @phpstan-ignore return.type
            new static($matched),
            new static($unmatched)
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Set::emptyCopy() To create an empty copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::add() To add values into the data structure.
     */
    public function union (iterable $values):static {

        $result = $this->fork();

        foreach ($this->storage->iterate() as $value)
            $result->add($value);

        return $result;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add values into the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check if the data structure contains a value.
     */
    public function intersection (iterable $values):static {

        $other = $this->storage->emptyCopy();

        foreach ($values as $value)
            $other->add($value);

        $result = $this->storage->emptyCopy();

        foreach ($this as $value)
            if ($other->contains($value))
                $result->add($value);

        return new static($result);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add values into the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check if the data structure contains
     * a value.
     */
    public function difference (iterable $values):static {

        $other = $this->storage->emptyCopy();

        foreach ($values as $value)
            $other->add($value);

        $result = $this->storage->emptyCopy();

        foreach ($this as $value)
            if (!$other->contains($value))
                $result->add($value);

        return new static($result);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the data structure.
     * @uses \FireHub\Foundation\DataStructure\Storage::add() To add values into the data structure.
     * @uses \FireHub\Foundation\DataStructure\Set::contains() To check if the data structure contains
     * a value.
     */
    public function symmetricDifference (iterable $values):static {

        $other = $this->storage->emptyCopy();

        foreach ($values as $value)
            $other->add($value);

        $result = $this->storage->emptyCopy();

        foreach ($this as $value)
            if (!$other->contains($value))
                $result->add($value);

        foreach ($other->iterate() as $value)
            if (!$this->contains($value))
                $result->add($value);

        return new static($result);

    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::size() To get the size of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Runtime\Random::number() To generate a random number.
     */
    public function random ():Maybe {

        $size = $this->storage->size();

        if ($size === 0)
            return new None;

        $random = Runtime\Random::number(0, $size - 1);
        $position = 0;

        foreach ($this->storage->iterate() as $value)
            if ($position++ === $random)
                return new Some($value);

        return new None;

    }

    /**
     * {@inheritDoc}
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::size() To get the size of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Core\Boundary\Capability\Mutation\ValueMutation::add() To add selected values to the storage.
     * @uses \FireHub\Runtime\Math::min() To get the minimum of two values.
     * @uses \FireHub\Runtime\Random::number() To generate a random number
     */
    public function sample (int $size):static {

        $size = Runtime\Math::min($size, $this->storage->size());

        $storage = $this->storage->emptyCopy();

        if ($size === 0)
            return new static($storage);

        $sample = [];

        $position = 0;

        foreach ($this->storage->iterate() as $value) {

            if ($position < $size)
                $sample[$position] = $value;
            else {

                $random = Runtime\Random::number(0, $position);

                if ($random < $size)
                    $sample[$random] = $value;

            }

            $position++;

        }

        foreach ($sample as $value)
            $storage->add($value);

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