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

use FireHub\Core\Boundary\Type\DataStructure\Collection\Map as MapBoundary;
use FireHub\Core\Boundary\Capability\ {
    Access\KeyAccess,
    Conversion\Arrayable,
    Measurement\Metrics,
    Mutation\KeyMutation,
    Transformation\Filterable, Transformation\KeySortable, Transformation\Mappable, Transformation\Rejectable,
    Transformation\Sortable,
    Cloneable, Forkable, Freezable, Thawable
};
use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\ {
    Order, MutationOutcome
};
use FireHub\Foundation\DataStructure\Storage\ {
    FixedStorage, HashStorage
};
use FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash;
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    ArrayInit, EmptyInit
};
use FireHub\Foundation\DataStructure\Boundary\Transformation\ {
    Chunkable, Groupable, Partitionable, Reversible, Shufflable, Skippable, Splittable, Takeable
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
use FireHub\Runtime;
use Traversable;

/**
 * ### Map data structure
 *
 * Represents an associative collection of key-value pairs with deterministic access to values through their
 * corresponding keys.
 *
 * Map provides the Foundation implementation of the Core map contract and serves as a general-purpose associative
 * collection abstraction within the FireHub data structure ecosystem.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Collection\Map<TKey, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, array{key: TKey, value: TValue}>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\KeyMutation<TKey, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<TKey, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Rejectable<TKey, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Sortable<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\KeySortable<TKey>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Chunkable<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Splittable<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Groupable<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Partitionable<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Reversible<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Shufflable<TKey, TValue>
 *
 * @phpstan-type StorageType = (
 *     Storage<TKey, TValue>
 *     &Cloneable
 *     &Forkable
 *     &Metrics
 *     &KeyAccess<TKey, TValue>
 *     &KeyMutation<TKey, TValue>
 *     &Sortable<TValue>
 *     &KeySortable<TKey>
 * )
 */
class Map implements MapBoundary, Arrayable, Cloneable, Forkable, Freezable, Thawable, KeyMutation, Mappable,
    Rejectable, Sortable, KeySortable, Chunkable, Splittable, Groupable, Partitionable, Takeable, Skippable, Reversible,
    Shufflable {

    /**
     * ### Freeze state
     * @since 1.0.0
     */
    use HasFreezeState;

    /**
     * ### Provides countable capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Aggregation\CanCount<TKey, TValue>
     */
    use CanCount;

    /**
     * ### Provides multiplicity capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Transformation\CanMultiplicity<TKey, TValue>
     */
    use CanMultiplicity;

    /**
     * ### Provides rejection capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Transformation\CanReject<TKey, TValue>
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
        protected Storage&Cloneable&Forkable&Metrics&KeyAccess&KeyMutation&Sortable&KeySortable $storage
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->toArray();
     *
     * // [
     * //   ['key' => 'x', 'value' => 1],
     * //   ['key' => 'y', 'value' => 2],
     * //   ['key' => 'z', 'value' => 3]
     * // ]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     */
    public function toArray ():array {

        $array = [];
        foreach ($this->storage->iterate() as $key => $value)
            $array[] = [
                'key' => $key,
                'value' => $value
            ];

        return $array;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->copy();
     *
     * // ['x' => 1, 'y' => 2, 'z' => 3]
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
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->fork();
     *
     * // ['x' => 1, 'y' => 2, 'z' => 3]
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
     * @uses \FireHub\Foundation\DataStructure\Map::fork() To create a fork of the data structure.
     * @uses \FireHub\Foundation\DataStructure\Map::thawState() To thaw the state of the data structure.
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
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->isEmpty();
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
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->size();
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
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->has('x');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::has() To check if the storage has a key.
     */
    public function has (mixed $key):bool {

        return $this->storage->has($key);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->get('x');
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::get() To get the value associated with the specified key.
     */
    public function get (mixed $key):Maybe {

        return $this->storage->get($key);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->set('x', 11)
     *
     * // MutationOutcome::UPDATED
     *
     * $map->toArray();
     *
     * // [
     * //   ['key' => 'x', 'value' => 11],
     * //   ['key' => 'y', 'value' => 2],
     * //   ['key' => 'z', 'value' => 3]
     * // ]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::set() To set the value associated with the specified key.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function set (mixed $key, mixed $value):MutationOutcome {

        $this->guardMutable();

        return $this->storage->set($key, $value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Map;
     * use FireHub\Foundation\DataStructure\Storage\HashStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $map = new Map(new HashStorage(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])));
     *
     * $map->remove('x')
     *
     * // MutationOutcome::REMOVED
     *
     * $map->toArray();
     *
     * // [
     * //   ['key' => 'y', 'value' => 2],
     * //   ['key' => 'z', 'value' => 3]
     * // ]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::remove() To remove the specified key from the storage.
     * @uses \FireHub\Foundation\State\HasFreezeState::guardMutable() To check if the data structure is mutable.
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the data structure is frozen.
     */
    public function remove (mixed $key):MutationOutcome {

        $this->guardMutable();

        return $this->storage->remove($key);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::set() To insert mapped values into the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::map() To map the values in the storage using the provided
     * callback.
     */
    public function map (callable $callback):static {

        if ($this->storage instanceof Mappable)
            return new static($this->storage->map($callback));

        $storage = $this->storage->emptyCopy();
        foreach ($this->storage->iterate() as $key => $value)
            $storage->set($key, $callback($value, $key));

        return new static($storage);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::set() To insert mapped values into the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::filter() To filter the values in the storage using the provided
     * callback.
     */
    public function filter (callable $callback):static {

        if ($this->storage instanceof Filterable)
            return new static($this->storage->filter($callback));

        $storage = $this->storage->emptyCopy();
        foreach ($this->storage->iterate() as $key => $value)
            if ($callback($value, $key))
                $storage->set($key, $value);

        return new static($storage);

    }

    /**
     * ### Creates a Select instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Select<TKey, TValue, $this> A select transformation of
     * the data structure.
     */
    public function select ():Select {

        /** @var Select<TKey, TValue, $this> */
        return new Select($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Sortable::sort() To sort the values in the storage.
     */
    public function sort (Order $order = Order::ASC):static {

        return new static($this->storage->sort($order));

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Sortable::sortKeys() To sort the values in the storage.
     */
    public function sortKeys (Order $order = Order::ASC):static {

        return new static($this->storage->sortKeys($order));

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Sortable::sortWith() To sort the values in the storage.
     */
    public function sortWith (callable $comparator):static {

        return new static($this->storage->sortWith($comparator));

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Transformation\KeySortable::sortKeysWith() To sort the keys in the
     * storage.
     */
    public function sortKeysWith (callable $comparator):static {

        return new static($this->storage->sortKeysWith($comparator));

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
     * @uses \FireHub\Foundation\DataStructure\Storage::set() To insert values at the storage.
     */
    public function chunkBy (callable $callback):Stream {

        return new Stream(
            new FactorySource(
                function () use ($callback):iterable {

                    $storage = $this->storage->emptyCopy();
                    $has_values = false;

                    foreach ($this->storage->iterate() as $key => $value) {

                        if ($has_values && $callback($value, $key)) {

                            yield new static($storage);

                            $storage = $this->storage->emptyCopy();

                        }

                        $storage->set($key, $value);
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
     * @return \FireHub\Foundation\DataStructure\Transformation\Chunk<TKey, TValue, $this> A chunk of the data
     * structure.
     */
    public function chunk ():Chunk {

        /** @var Chunk<TKey, TValue, $this> */
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
     * @uses \FireHub\Foundation\DataStructure\Storage::set() To insert values at the storage.
     */
    public function splitBy (callable $callback):Stream {

        return new Stream(
            new FactorySource(
                function () use ($callback):iterable {

                    $storage = $this->storage->emptyCopy();
                    $has_values = false;

                    foreach ($this->storage->iterate() as $key => $value) {

                        if ($callback($value, $key)) {

                            if ($has_values)
                                yield new static($storage);

                            $storage = $this->storage->emptyCopy();
                            $has_values = false;

                            continue;

                        }

                        $storage->set($key, $value);
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
     * @return \FireHub\Foundation\DataStructure\Transformation\Split<TKey, TValue, $this> A split transformation of the
     * data structure.
     */
    public function split ():Split {

        /** @var Split<TKey, TValue, $this> */
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

        foreach ($this->storage->iterate() as $key => $value) {

            $identity = $selector($value, $key);

            if (isset($groups[$identity])) {

                $groups[$identity]->set($key, $value);

                continue;

            }

            $storage = $this->storage->emptyCopy();

            $storage->set($key, $value);

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
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create empty storages for the generated partitions.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the source storage.
     * @uses \FireHub\Foundation\DataStructure\Map::set() To associate a partition with its identity.
     */
    public function partition (callable $callback):Tuple {

        $matched = $this->storage->emptyCopy();
        $unmatched = $this->storage->emptyCopy();

        foreach ($this->storage->iterate() as $key => $value) {

            if ($callback($value, $key))
                $matched->set($key, $value);
            else
                $unmatched->set($key, $value);

        }

        return new Tuple( // @phpstan-ignore return.type
            new FixedStorage( // @phpstan-ignore argument.type
                2,
                new ArrayInit([
                    new static($matched),
                    new static($unmatched)
                ])
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackInsertion::set() To insert values at the storage.
     */
    public function takeWhile (callable $callback):static {

        $storage = $this->storage->emptyCopy();

        foreach ($this->storage->iterate() as $key => $value) {

            if (!$callback($value, $key))
                break;

            $storage->set($key, $value);

        }

        return new static($storage);

    }

    /**
     * ### Creates a Take instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Take<TKey, TValue, $this> A take transformation of the
     * data structure.
     */
    public function take ():Take {

        /** @var Take<TKey, TValue, $this> */
        return new Take($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Core\Boundary\Capability\Mutation\BackInsertion::set() To insert values at the storage.
     */
    public function skipWhile (callable $callback):static {

        $storage = $this->storage->emptyCopy();
        $skipping = true;

        foreach ($this->storage->iterate() as $key => $value) {

            if ($skipping) {

                if ($callback($value, $key))
                    continue;

                $skipping = false;

            }

            $storage->set($key, $value);

        }

        return new static($storage);

    }

    /**
     * ### Creates a Skip instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Skip<TKey, TValue, $this> A skip transformation of the
     * data structure.
     */
    public function skip ():Skip {

        /** @var Skip<TKey, TValue, $this> */
        return new Skip($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::set() To insert values to the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::reverse() To reverse the values in the storage.
     * @uses \FireHub\Foundation\DataStructure\Map::toArray() To convert the storage to an array.
     * @uses \FireHub\Runtime\Arr\Transform::reverse() To reverse the array.
     */
    public function reverse ():static {

        if ($this->storage instanceof Reversible)
            return new static($this->storage->reverse());

        $storage = $this->storage->emptyCopy();

        $entries = Runtime\Arr\Transform::reverse($this->toArray());

        foreach ($entries as $entry)
            $storage->set($entry['key'], $entry['value']);

        return new static($storage);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::emptyCopy() To create an empty copy of the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::set() To insert values into the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::shuffle() To shuffle the values in the storage.
     * @uses \FireHub\Foundation\DataStructure\Map::toArray() To convert the map entries to an array.
     * @uses \FireHub\Runtime\Arr\Ordering::shuffle() To shuffle the array.
     */
    public function shuffle ():static {

        if ($this->storage instanceof Shufflable)
            return new static($this->storage->shuffle());

        $entries = $this->toArray();

        Runtime\Arr\Ordering::shuffle($entries);

        $storage = $this->storage->emptyCopy();

        foreach ($entries as $entry)
            $storage->set($entry['key'], $entry['value']);

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