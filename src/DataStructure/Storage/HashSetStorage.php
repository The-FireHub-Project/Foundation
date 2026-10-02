<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Storage;

use FireHub\Core\Boundary\Capability\ {
    Access\ValueAccess,
    Measurement\Metrics,
    Mutation\ValueMutation,
    Cloneable, Forkable
};
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Hash\Engine;

/**
 * ### Provides a storage implementation for hash-based unique values
 *
 * Hash set storage maintains unique values using a hash engine where each set value is represented as a hash key
 * associated with a constant boolean marker.
 *
 * The underlying hash engine defines how values are stored and resolved. Array-backed engines may provide native
 * PHP array-key lookup for supported values, while bucket-based engines may provide hashing and equality semantics
 * for arbitrary value types.
 *
 * Hash set storage adapts key-value hash engine operations to value-oriented set storage semantics. Values are
 * exposed as sequentially indexed elements while uniqueness, lookup, and removal are delegated to the underlying
 * hash engine.
 *
 * The implementation is designed as a general-purpose storage mechanism for unique values and does not impose the
 * public semantics of a particular data structure. Higher-level structures such as sets may use hash set storage
 * according to the capabilities they require.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Access\ValueAccess<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\ValueMutation<TValue>
 */
final readonly class HashSetStorage implements Storage, Cloneable, Forkable, Metrics, ValueAccess, ValueMutation {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Engine<TValue, true> $engine <p>
     * The hash engine used to store set values as hash keys.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Engine $engine
    ) {}


    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::emptyCopy() To create an empty copy of the hash
     * engine.
     */
    public function emptyCopy ():self {

        /** @var self<TValue> */
        return new self(
            $this->engine->emptyCopy()
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::copy() To create a copy of the hash engine.
     */
    public function copy ():self {

        return new self(
            $this->engine->copy()
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::fork() To create a fork of the hash engine.
     */
    public function fork ():self {

        return new self(
            $this->engine->fork()
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::iterate() To iterate over the hash engine.
     */
    public function iterate ():iterable {

        $index = 0;
        foreach ($this->engine->iterate() as $value => $_)
            yield $index++ => $value;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashSetStorage::size() To get the size of the storage.
     */
    public function isEmpty ():bool {

        return $this->size() === 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::size() To get the size of the hash engine.
     */
    public function size ():int {

        return $this->engine->size();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::has() To determine whether the hash engine contains
     * the specified value as a key.
     */
    public function contains (mixed $value):bool {

        return $this->engine->has($value);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::has() To determine whether the value already
     * exists.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::set() To store the value as a hash key.
     */
    public function add (mixed $value):MutationOutcome {

        if ($this->engine->has($value))
            return MutationOutcome::ALREADY_EXISTS;

        $this->engine->set($value, true);

        return MutationOutcome::CREATED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::remove() To remove the specified value from the
     * hash engine.
     */
    public function remove (mixed $value):MutationOutcome {

        return $this->engine->remove($value);

    }

}