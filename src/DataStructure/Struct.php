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

use FireHub\Core\Boundary\Type\DataStructure\Record\Struct as StructBoundary;
use FireHub\Core\Boundary\Capability\ {
    Access\KeyAccess,
    Conversion\Arrayable,
    Measurement\Metrics,
    Cloneable
};
use FireHub\Core\Type\Maybe;
use FireHub\Runtime;
use Traversable;

/**
 * ### Struct data structure
 *
 * Represents a fixed-size keyed Record whose elements are identified by integer or string keys.
 *
 * Each key identifies a distinct component of a single composite value and forms part of the Struct's defined
 * structure. The set of keys remains fixed for the lifetime of the Struct.
 *
 * Unlike a Map, which represents a dynamically changing collection of key-value associations, a Struct represents
 * a single structured value with a fixed set of keyed elements. Insertion and removal operations are therefore not
 * part of the Struct.
 *
 * The Struct provides keyed access to its elements while remaining immutable from the perspective of its public
 * interface. Existing elements cannot be replaced, and the defined structure cannot be extended or reduced after
 * construction.
 *
 * The iteration order of the underlying Storage does not form part of the Struct's semantic structure. Elements are
 * identified by their keys rather than by their position within the iteration sequence.
 *
 * Struct elements may contain values of different types, represented collectively by the TValue template type.
 * @since 1.0.0
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Record\Struct<TKey, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Conversion\Arrayable<TKey, TValue>
 *
 * @phpstan-type StorageType = (
 *     Storage<TKey, TValue>
 *     &Cloneable
 *     &Metrics
 *     &KeyAccess<TKey, TValue>
 * )
 */
class Struct implements StructBoundary, Arrayable, Cloneable {

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
        protected Storage&Cloneable&Metrics&KeyAccess $storage
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Struct;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $struct = new Struct(new HashStorage(new ArrHash(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3]))));
     *
     * $struct->toArray();
     *
     * // ['x' => 1, 'y' => 2, 'z' => 3]
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
     * use FireHub\Foundation\DataStructure\Struct;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $struct = new Struct(new HashStorage(new ArrHash(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3]))));
     *
     * $struct->copy();
     *
     * // ['x' => 1, 'y' => 2, 'z' => 3]
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
     * use FireHub\Foundation\DataStructure\Struct;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $struct = new Struct(new HashStorage(new ArrHash(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3]))));
     *
     * $struct->isEmpty();
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
     * use FireHub\Foundation\DataStructure\Struct;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $struct = new Struct(new HashStorage(new ArrHash(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3]))));
     *
     * $struct->size();
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
     * use FireHub\Foundation\DataStructure\Struct;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $struct = new Struct(new HashStorage(new ArrHash(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3]))));
     *
     * $struct->has('x');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::has() To check if the storage has a value at the specified
     * index.
     */
    public function has (mixed $key):bool {

        return $this->storage->has($key);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\DataStructure\Struct;
     * use FireHub\Foundation\DataStructure\Storage\FixedStorage;
     * use FireHub\Foundation\DataStructure\Storage\Initialization\ArrayInit;
     *
     * $struct = new Struct(new HashStorage(new ArrHash(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3]))));
     *
     * $struct->get('x');
     *
     * // Maybe(1)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::get() To get the value at the specified index.
     */
    public function get (mixed $key):Maybe {

        return $this->storage->get($key);

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