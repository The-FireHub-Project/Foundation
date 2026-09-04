<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Storage;

use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Hash\Engine;
use FireHub\Foundation\DataStructure\Storage\Capability\ {
    KeyAccess, KeyMutation, Metrics
};

/**
 * ### Provides a storage implementation for hash-based key-value pairs
 *
 * Hash storage maintains values in an associative key-value representation, allowing values to be stored and
 * retrieved through their associated keys. Keys are preserved as provided by the initialization strategy, and
 * values retain the insertion order defined by the underlying PHP array representation.
 *
 * The implementation is designed as a general-purpose associative storage mechanism and does not impose the
 * public semantics of a particular data structure. Higher-level structures such as maps and associative
 * collections may use hash storage according to the capabilities they require.
 *
 * Hash storage manages the underlying representation and storage behavior while the consuming data structure
 * defines the public API and semantics exposed to its users.
 * @since 1.0.0
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\KeyAccess<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Storage\Capability\KeyMutation<TKey, TValue>
 */
final class HashStorage implements Storage, Metrics, KeyAccess, KeyMutation {

    /**
     * ### Underlying hash engine
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Engine<TKey, TValue> $engine <p>
     * The hash engine to use for storage.
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
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::iterate() To iterate over the hash engine.
     */
    public function iterate ():iterable {

        return $this->engine->iterate();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashStorage::size() To get the size of the storage.
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
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::has() To check if the hash engine has a value for
     * the specified key.
     */
    public function has (mixed $key):bool {

        return $this->engine->has($key);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::get() To get the value associated with the
     * specified key.
     */
    public function get (mixed $key):Maybe {

        return $this->engine->get($key);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::set() To set the value associated with the
     * specified key.
     */
    public function set (mixed $key, mixed $value):MutationOutcome {

        return $this->engine->set($key, $value);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::remove() To remove the value associated with the
     * specified key.
     */
    public function remove (mixed $key):MutationOutcome {

        return $this->engine->remove($key);

    }

}