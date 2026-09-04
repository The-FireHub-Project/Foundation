<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.0
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Storage\Hash\Engine;

use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage\Initializer;
use FireHub\Foundation\DataStructure\Storage\Hash\Engine;
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Runtime;

/**
 * ### Provides an array-backed hash storage engine
 *
 * Array hash engine uses a PHP array as the underlying representation for storing and managing key-value pairs.
 * It provides the default hash engine implementation for hash-based storage while preserving the keys and values
 * supplied by the initialization strategy.
 *
 * The engine manages the underlying array representation while the consuming storage defines the public API and
 * semantics exposed to its users.
 * @since 1.0.0
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Engine<TKey, TValue>
 */
final class ArrHash implements Engine {

    /**
     * ### Underlying hash data
     * @since 1.0.0
     *
     * @var array<TKey, TValue>
     */
    private array $data;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initializer::initialize() To initialize the hash.
     * @uses \FireHub\Runtime\Iterator::toArray() To materialize the initial key-value pairs.
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Initializer<TKey, TValue> $initializer <p>
     * Initializes the hash with the provided key-value pairs.
     * </p>
     *
     * @return void
     */
    public function __construct (Initializer $initializer) {

        $this->data = Runtime\Iterator::toArray($initializer->initialize());

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function iterate ():iterable {

        return $this->data;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the size of the hash.
     */
    public function size ():int {

        return Runtime\Arr\Inspection::count($this->data);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::keyExists() To check if the hash has a key.
     */
    public function has (mixed $key):bool {

        return Runtime\Arr\Access::keyExists($this->data, $key);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash::has() To check if the hash has a key.
     */
    public function get (mixed $key):Maybe {

        if ($this->has($key))
            return new Some($this->data[$key]); // @phpstan-ignore offsetAccess.notFound

        return new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function set (mixed $key, mixed $value):MutationOutcome {

        $outcome = $this->has($key)
            ? MutationOutcome::UPDATED
            : MutationOutcome::CREATED;

        $this->data[$key] = $value;

        return $outcome;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function remove (mixed $key):MutationOutcome {

        if (!$this->has($key))
            return MutationOutcome::NOT_FOUND;

        unset($this->data[$key]);

        return MutationOutcome::REMOVED;

    }

}