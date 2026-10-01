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

use FireHub\Core\Boundary\Type\DataStructure\Record\Tuple as TupleBoundary;
use FireHub\Core\Boundary\Capability\ {
    Access\IndexAccess,
    Conversion\Arrayable,
    Measurement\Metrics,
    Mutation\IndexReplacement,
    Cloneable
};
use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible;
use FireHub\Foundation\DataStructure\Concern\ {
    Aggregation\CanReduce,
    Query\CanFind, Query\CanMatch,
    Transformation\CanCombine
};
use FireHub\Runtime;
use Traversable;

/**
 * ### Tuple data structure
 *
 * Represents a fixed-size positional Record whose elements are identified by zero-based integer indexes.
 *
 * Elements are arranged in a defined linear order, where each position represents a distinct component of a single
 * composite value. The number and positions of elements remain fixed for the lifetime of the Tuple.
 *
 * Unlike dynamically sized Collections, a Tuple does not expose insertion or removal operations. Its structure is
 * established during construction and cannot be extended, reduced, or reordered. Values associated with existing
 * positions may be replaced without changing the Tuple's structure.
 *
 * The Tuple delegates element storage and access to an underlying Storage implementation that provides positional
 * access, replacement, and measurement capabilities.
 *
 * Tuple elements may contain values of different types, represented collectively by the TValue template type.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Record\Tuple<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\IndexReplacement<TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible<TValue>
 *
 * @phpstan-type StorageType = (
 *     Storage<int, TValue>
 *     &Cloneable
 *     &Metrics
 *     &IndexAccess<TValue>
 *     &IndexReplacement<TValue>
 * )
 */
class Tuple implements TupleBoundary, Arrayable, Cloneable, IndexReplacement, Reducible {

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
     * ### Provides combining capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Transformation\CanCombine<TValue>
     */
    use CanCombine;

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
        protected Storage&Cloneable&Metrics&IndexAccess&IndexReplacement $storage
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Tuple;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $tuple = new Tuple(new FixedStorage(3, new ArrayInit(['one', 'two', 'three'])));
     *
     * $tuple->toArray();
     *
     * // ['one', 'two', 'three']
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
     * use FireHub\Foundation\DataStructure\Tuple;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $tuple = new Tuple(new FixedStorage(3, new ArrayInit(['one', 'two', 'three'])));
     *
     * $vector->copy();
     *
     * // ['one', 'two', 'three']
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
     * use FireHub\Foundation\DataStructure\Tuple;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $tuple = new Tuple(new FixedStorage(3, new ArrayInit(['one', 'two', 'three'])));
     *
     * $tuple->isEmpty();
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
     * use FireHub\Foundation\DataStructure\Tuple;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $tuple = new Tuple(new FixedStorage(3, new ArrayInit(['one', 'two', 'three'])));
     *
     * $tuple->size();
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
     * use FireHub\Foundation\DataStructure\Tuple;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $tuple = new Tuple(new FixedStorage(3, new ArrayInit(['one', 'two', 'three'])));
     *
     * $tuple->has(0);
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
     * use FireHub\Foundation\DataStructure\Tuple;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $tuple = new Tuple(new FixedStorage(3, new ArrayInit(['one', 'two', 'three'])));
     *
     * $tuple->get(0);
     *
     * // Maybe('one')
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
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Mutation\IndexReplacement::replace() To replace the value at the specified
     * index.
     */
    public function replace (int $index, mixed $value):MutationOutcome {

        return $this->storage->replace($index, $value);

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