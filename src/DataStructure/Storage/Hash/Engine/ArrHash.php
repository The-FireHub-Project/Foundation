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

namespace FireHub\Foundation\DataStructure\Storage\Hash\Engine;

use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage\Initializer;
use FireHub\Foundation\DataStructure\Storage\Hash\Engine;
use FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit;
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Foundation\State\ {
    HasCopyOnWriteState, SharedState
};
use FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashKeyException;
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
 *
 * @phpstan-type State array<TKey, TValue>
 */
final class ArrHash implements Engine {

    /**
     * ### Copy-on-write state
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\State\HasCopyOnWriteState<State>
     */
    use HasCopyOnWriteState;

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

        $this->state = new SharedState( // @phpstan-ignore assign.propertyType
            Runtime\Iterator::toArray($initializer->initialize())
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit To initialize the storage with an empty
     * array.
     */
    public function emptyCopy ():self {

        /** @var self<TKey, TValue> */
        return new self(new EmptyInit);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Copy::deep() To deep copy the storage.
     *
     * @throws \FireHub\Runtime\Exception\CopyObjectException If the object's copying fails.
     */
    public function copyData (mixed $data):array {

        /** @var State */
        return Runtime\Copy::deep($data);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the underlying data.
     */
    public function iterate ():iterable {

        return $this->state->data();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the size of the hash.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the underlying data.
     */
    public function size ():int {

        return Runtime\Arr\Inspection::count($this->state->data());

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::keyExists() To check if the hash has a key.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the underlying data.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash::isInvalidKey() To check if the key is
     * invalid.
     */
    public function has (mixed $key):bool {

        if (!$this->isValidKey($key)) return false;

        return Runtime\Arr\Access::keyExists($this->state->data(), $key);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash::has() To check if the hash has a key.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the underlying data.
     */
    public function get (mixed $key):Maybe {

        if ($this->has($key))
            return new Some($this->state->data()[$key]); // @phpstan-ignore offsetAccess.notFound

        return new None();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash::detach() To detach the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the underlying data.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash::isInvalidKey() To check if the key is
     * invalid.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidHashKeyException If the key is invalid.
     */
    public function set (mixed $key, mixed $value):MutationOutcome {

        if (!$this->isValidKey($key))
            throw new InvalidHashKeyException;

        $outcome = $this->has($key)
            ? MutationOutcome::UPDATED
            : MutationOutcome::CREATED;

        $this->detach();

        $this->state->data()[$key] = $value;

        return $outcome;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash::has() To check if the hash has a key.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash::detach() To detach the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the underlying data.
     */
    public function remove (mixed $key):MutationOutcome {

        if (!$this->has($key))
            return MutationOutcome::NOT_FOUND;

        $this->detach();

        $data = &$this->state->data();

        unset($data[$key]);

        return MutationOutcome::REMOVED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Transform::map() To map the storage.
     */
    public function map (callable $callback):self {

        return clone($this, [ // @phpstan-ignore assign.propertyType
            'state' => new SharedState(
                Runtime\Arr\Transform::map(
                    $this->state->data(),
                    $callback
                )
            )
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Transform::filter() To filter the storage.
     */
    public function filter (callable $callback):self {

        return clone($this, [ // @phpstan-ignore assign.propertyType
            'state' => new SharedState(
                Runtime\Arr\Transform::filter(
                    $this->state->data(),
                    $callback
                )
            )
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Transform::reverse() To reverse the storage.
     */
    public function reverse ():self {

        return clone($this, [ // @phpstan-ignore assign.propertyType
            'state' => new SharedState(
                Runtime\Arr\Transform::reverse(
                    $this->state->data(), true
                )
            )
        ]);

    }

    /**
     * ### Checks if the key is valid
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::int() To check if the key is an integer.
     * @uses \FireHub\Runtime\DataIs::string() To check if the key is a string.
     *
     * @phpstan-assert-if-true array-key $key
     *
     * @return bool Whether the key is valid.
     */
    private function isValidKey (mixed $key):bool {

        return Runtime\DataIs::int($key) || Runtime\DataIs::string($key);

    }

}