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

namespace FireHub\Foundation\DataStructure\Storage;

use FireHub\Core\Boundary\Capability\ {
    Access\ValueAccess,
    Measurement\Metrics,
    Mutation\ValueMutation,
    Cloneable, Forkable
};
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Hash\Strategy;
use FireHub\Foundation\State\ {
    HasCopyOnWriteState, SharedState
};
use FireHub\Runtime;

/**
 * ### Provides a storage implementation for hash-based unique values
 *
 * Hash set storage maintains unique values using a hash-based representation, allowing values to be efficiently
 * stored, located, and removed according to the hashing and equality semantics defined by the configured hash
 * strategy.
 *
 * Values are organized into hash buckets according to their calculated hashes. Hash collisions are resolved
 * through equality comparison, ensuring that logically equal values occur at most once within the storage.
 *
 * The implementation is designed as a general-purpose storage mechanism for unique values and does not impose the
 * public semantics of a particular data structure. Higher-level structures such as sets may use hash set storage
 * according to the capabilities they require.
 *
 * Hash set storage manages the underlying hash representation, uniqueness enforcement, and storage behavior while
 * the consuming data structure defines the public API and semantics exposed to its users.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Access\ValueAccess<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\ValueMutation<TValue>
 *
 * @phpstan-type State array{
 *     buckets: array<string, list<TValue>>,
 *     size: int
 * }

 */
final class HashSetStorage implements Storage, Cloneable, Forkable, Metrics, ValueAccess, ValueMutation {

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
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TValue> $strategy <p>
     * The hash strategy used to calculate hash values for values.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private readonly Strategy $strategy
    ) {

        /** @var State $state */
        $state = [
            'buckets' => [],
            'size' => 0
        ];

        $this->state = new SharedState($state);

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
    protected function copyData (mixed $data):array {

        /** @var State */
        return Runtime\Copy::deep($data);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function iterate ():iterable {

        $index = 0;

        foreach ($this->state->data()['buckets'] as $bucket)
            foreach ($bucket as $value)
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
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function size ():int {

        /** @var non-negative-int */
        return $this->state->data()['size'];

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To calculate the hash value of the
     * specified value.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the specified value
     * with the stored values.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function contains (mixed $value):bool {

        $hash = $this->strategy->hash($value);
        $data = &$this->state->data();

        if (!isset($data['buckets'][$hash]))
            return false;

        foreach ($data['buckets'][$hash] as $stored)
            if ($this->strategy->equals($stored, $value))
                return true;

        return false;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashSetStorage::detach() To detach the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To calculate the hash value of the
     * specified value.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the specified value
     * with the stored values.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function add (mixed $value):MutationOutcome {

        $hash = $this->strategy->hash($value);

        foreach ($this->state->data()['buckets'][$hash] ?? [] as $stored)
            if ($this->strategy->equals($stored, $value))
                return MutationOutcome::ALREADY_EXISTS;

        $this->detach();

        $data = &$this->state->data();

        $data['buckets'][$hash][] = $value;
        $data['size']++;

        return MutationOutcome::CREATED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashSetStorage::detach() To detach the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To calculate the hash value of the
     * specified value.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the specified value
     * with the stored values.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Structure::splice() To remove the specified value from the storage.
     */
    public function remove (mixed $value):MutationOutcome {

        $hash = $this->strategy->hash($value);

        if (!isset($this->state->data()['buckets'][$hash]))
            return MutationOutcome::NOT_FOUND;

        foreach ($this->state->data()['buckets'][$hash] as $index => $stored) {

            if (!$this->strategy->equals($stored, $value))
                continue;

            $this->detach();

            $data = &$this->state->data();

            Runtime\Arr\Structure::splice($data['buckets'][$hash], $index, 1); // @phpstan-ignore offsetAccess.notFound

            if ($data['buckets'][$hash] === [])
                unset($data['buckets'][$hash]);

            $data['size']--;

            return MutationOutcome::REMOVED;

        }

        return MutationOutcome::NOT_FOUND;

    }

}