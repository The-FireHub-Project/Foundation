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
    Access\MultiplicityAccess,
    Measurement\DistinctMetrics,
    Mutation\MultiplicityMutation,
    Transformation\Filterable, Transformation\Mappable,
    Cloneable, Forkable
};
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Hash\Strategy;
use FireHub\Foundation\State\ {
    HasCopyOnWriteState, SharedState
};
use FireHub\Foundation\DataStructure\Storage\Exception\InvalidOccurrencesException;
use FireHub\Runtime;

/**
 * ### Provides a storage implementation for hash-based value multiplicities
 *
 * Hash bag storage maintains values together with the number of times each logically distinct value occurs,
 * allowing values to be efficiently stored, located, counted, and removed according to the hashing and equality
 * semantics defined by the configured hash strategy.
 *
 * Values are organized into hash buckets according to their calculated hashes. Hash collisions are resolved
 * through equality comparison, ensuring that logically equal values share the same occurrence count while
 * different values may coexist within the same hash bucket.
 *
 * The storage tracks both the total number of stored occurrences and the number of logically distinct values.
 * Adding an existing value increases its occurrence count, while removing a value decreases its occurrence count
 * and removes the corresponding entry when its final occurrence is removed.
 *
 * The implementation is designed as a general-purpose storage mechanism for value multiplicities and does not
 * impose the public semantics of a particular data structure. Higher-level structures such as bags may use hash
 * bag storage according to the capabilities they require.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Access\MultiplicityAccess<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\MultiplicityMutation<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Filterable<int, TValue>
 *
 * @phpstan-type State array{
 *     buckets: array<string, list<array{value: TValue, count: int}>>,
 *     size: int,
 *     distinct_size: int
 * }
 */
final class HashBagStorage implements Storage, Cloneable, Forkable, DistinctMetrics, MultiplicityAccess,
    MultiplicityMutation, Mappable, Filterable {

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
            'size' => 0,
            'distinct_size' => 0
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
            foreach ($bucket as $entry)
                for ($occurrence = 0; $occurrence < $entry['count']; $occurrence++)
                    yield $index++ => $entry['value'];

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashBagStorage::size() To get the size of the storage.
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

        /** @var positive-int */
        return $this->state->data()['size'];

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function distinctSize ():int {

        /** @var positive-int */
        return $this->state->data()['distinct_size'];

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

        foreach ($data['buckets'][$hash] as $entry)
            if ($this->strategy->equals($entry['value'], $value))
                return true;

        return false;

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
    public function frequency (mixed $value):int {

        $hash = $this->strategy->hash($value);
        $data = &$this->state->data();

        if (!isset($data['buckets'][$hash]))
            return 0;

        foreach ($data['buckets'][$hash] as $entry)
            if ($this->strategy->equals($entry['value'], $value))
                /** @var non-negative-int */
                return $entry['count'];

        return 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashBagStorage::detach() To detach the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To calculate the hash value of the
     * specified value.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the specified value
     * with the stored values.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidOccurrencesException If the specified number
     * of occurrences is less than or equal to zero.
     */
    public function add (mixed $value, int $count = 1):MutationOutcome {

        if ($count < 1)
            throw new InvalidOccurrencesException('The number of occurrences must be greater than zero.');

        $hash = $this->strategy->hash($value);

        foreach ($this->state->data()['buckets'][$hash] ?? [] as $index => $entry) {

            if (!$this->strategy->equals($entry['value'], $value))
                continue;

            $this->detach();

            $data = &$this->state->data();

            $data['buckets'][$hash][$index]['count'] += $count; // @phpstan-ignore-line
            $data['size'] += $count;

            return MutationOutcome::UPDATED;

        }

        $this->detach();

        $data = &$this->state->data();

        $data['buckets'][$hash][] = [
            'value' => $value,
            'count' => $count
        ];

        $data['size'] += $count;
        $data['distinct_size']++;

        return MutationOutcome::CREATED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashBagStorage::detach() To detach the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To calculate the hash value of the
     * specified value.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the specified value
     * with the stored values.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Structure::splice() To remove the specified value from the storage.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidOccurrencesException If the specified number
     * of occurrences is less than or equal to zero.
     */
    public function remove (mixed $value, int $count = 1):MutationOutcome {

        if ($count < 1)
            throw new InvalidOccurrencesException('The number of occurrences must be greater than zero.');

        $hash = $this->strategy->hash($value);

        if (!isset($this->state->data()['buckets'][$hash]))
            return MutationOutcome::NOT_FOUND;

        foreach ($this->state->data()['buckets'][$hash] as $index => $entry) {

            if (!$this->strategy->equals($entry['value'], $value))
                continue;

            $this->detach();

            $data = &$this->state->data();

            if ($entry['count'] > $count) {

                $data['buckets'][$hash][$index]['count'] -= $count; // @phpstan-ignore-line
                $data['size'] -= $count;

                return MutationOutcome::UPDATED;

            }

            Runtime\Arr\Structure::splice(
                $data['buckets'][$hash], // @phpstan-ignore offsetAccess.notFound
                $index,
                1
            );

            if ($data['buckets'][$hash] === [])
                unset($data['buckets'][$hash]);

            $data['size'] -= $entry['count'];
            $data['distinct_size']--;

            return MutationOutcome::REMOVED;

        }

        return MutationOutcome::NOT_FOUND;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashBagStorage::detach() To detach the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To calculate the hash value of the
     * specified value.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the specified value
     * with the stored values.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Structure::splice() To remove the specified value from the storage.
     */
    public function removeAll (mixed $value):MutationOutcome {

        $hash = $this->strategy->hash($value);

        if (!isset($this->state->data()['buckets'][$hash]))
            return MutationOutcome::NOT_FOUND;

        foreach ($this->state->data()['buckets'][$hash] as $index => $entry) {

            if (!$this->strategy->equals($entry['value'], $value))
                continue;

            $this->detach();

            $data = &$this->state->data();

            Runtime\Arr\Structure::splice(
                $data['buckets'][$hash], // @phpstan-ignore offsetAccess.notFound
                $index,
                1
            );

            if ($data['buckets'][$hash] === [])
                unset($data['buckets'][$hash]);

            $data['size'] -= $entry['count'];
            $data['distinct_size']--;

            return MutationOutcome::REMOVED;

        }

        return MutationOutcome::NOT_FOUND;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\HashBagStorage::add() To add the mapped value to the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     *
     * @template TMapped
     */
    public function map (callable $callback):self {

        /** @var self<TMapped> $mapped */
        $mapped = new self($this->strategy);

        $index = 0;
        foreach ($this->state->data()['buckets'] as $bucket) {

            foreach ($bucket as $entry) {

                $mapped->add(
                    $callback($entry['value'], $index++),
                    $entry['count'] // @phpstan-ignore argument.type
                );

            }

        }

        return $mapped; // @phpstan-ignore return.type

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function filter (callable $callback):self {

        $buckets = []; $size = 0; $distinct_size = 0;
        foreach ($this->state->data()['buckets'] as $hash => $bucket) {

            foreach ($bucket as $entry) {

                if (!$callback($entry['value']))
                    continue;

                $buckets[$hash][] = $entry;
                $size += $entry['count'];
                $distinct_size++;

            }

        }

        /** @var State $state */
        $state = [
            'buckets' => $buckets,
            'size' => $size,
            'distinct_size' => $distinct_size
        ];

        return clone($this, [
            'state' => new SharedState($state)
        ]);

    }

}