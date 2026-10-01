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

use FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm;
use FireHub\Core\Type\Maybe;
use FireHub\Core\Meta\Enum\ {
    Order, MutationOutcome
};
use FireHub\Foundation\DataStructure\Storage\Initializer;
use FireHub\Foundation\DataStructure\Storage\Hash\ {
    Engine, Strategy
};
use FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit;
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Foundation\State\ {
    HasCopyOnWriteState, SharedState
};
use FireHub\Foundation\DataStructure\Storage\Exception\InvalidRangeLength;
use FireHub\Runtime;

/**
 * ### Provides a bucket-backed hash storage engine
 *
 * Bucket hash engine stores key-value pairs in hash buckets according to the hashing and equality semantics defined
 * by the configured hash strategy.
 *
 * Unlike the array-backed hash engine, bucket hash is not restricted to PHP array keys and can therefore support
 * arbitrary key types for which an appropriate hash strategy is available.
 *
 * Hash collisions are resolved by comparing keys within the corresponding bucket using the configured equality
 * strategy. Keys considered equal must produce the same hash, while different keys may share the same hash.
 *
 * The engine maintains the logical order of stored keys independently of the bucket representation. This allows
 * iteration and positional transformations such as slicing, reversing, shuffling, and sorting to preserve a
 * deterministic global order regardless of bucket distribution.
 *
 * Updating an existing key preserves its original key and logical position while replacing only the associated
 * value.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Hash\Engine<TKey, TValue>
 *
 * @phpstan-type Entry array{
 *     key: TKey,
 *     value: TValue
 * }
 *
 * @phpstan-type State array{
 *     buckets: array<string, list<Entry>>,
 *     order: list<TKey>,
 *     size: int
 * }
 */
final class BucketHash implements Engine {

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
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::initializeEntry() To initialize an entry.
     * @uses \FireHub\Foundation\DataStructure\Storage\Initializer::initialize() To initialize the hash with the
     * provided key-value pairs.
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Initializer<TKey, TValue> $initializer <p>
     * Initializes the hash with the provided key-value pairs.
     * </p>
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Strategy<TKey> $strategy <p>
     * Hashing and equality strategy used for keys.
     * </p>
     *
     * @return void
     */
    public function __construct (
        Initializer $initializer,
        private readonly Strategy $strategy
    ) {

        /** @var State $state */
        $state = [
            'buckets' => [],
            'order' => [],
            'size' => 0
        ];

        foreach ($initializer->initialize() as $key => $value)
            $this->initializeEntry($state, $key, $value); // @phpstan-ignore argument.type

        $this->state = new SharedState($state); // @phpstan-ignore assign.propertyType

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function emptyCopy ():self {

        /** @var self<TKey, TValue> */
        return new self(new EmptyInit, $this->strategy);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Copy::deep() To deep copy the engine state.
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
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::findEntry() To find an entry.
     */
    public function iterate ():iterable {

        foreach ($this->state->data()['order'] as $key) {

            $entry = $this->findEntry($key);

            if ($entry !== null)
                yield $entry['key'] => $entry['value'];

        }

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
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
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::findEntry() To find an entry.
     */
    public function has (mixed $key):bool {

        return $this->findEntry($key) !== null;

    }

    /**
     * @inheritDoc
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::findEntry() To find an entry.
     *
     * @since 1.0.0
     */
    public function get (mixed $key):Maybe {

        $entry = $this->findEntry($key);

        return $entry === null
            ? new None()
            : new Some($entry['value']);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To hash the key.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the key.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::detach() To detach the engine state.
     */
    public function set (mixed $key, mixed $value):MutationOutcome {

        $hash = $this->strategy->hash($key);

        foreach ($this->state->data()['buckets'][$hash] ?? [] as $index => $entry) {

            if (!$this->strategy->equals($entry['key'], $key))
                continue;

            $this->detach();

            $this->state->data()['buckets'][$hash][$index]['value'] = $value;

            return MutationOutcome::UPDATED;

        }

        $this->detach();

        $data = &$this->state->data();

        $data['buckets'][$hash][] = [
            'key' => $key,
            'value' => $value
        ];

        $data['order'][] = $key;
        $data['size']++;

        return MutationOutcome::CREATED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To hash the key.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the key.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::detach() To detach the engine state.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     */
    public function replace (mixed $key, mixed $value):MutationOutcome {

        $hash = $this->strategy->hash($key);

        foreach ($this->state->data()['buckets'][$hash] ?? [] as $index => $entry) {

            if (!$this->strategy->equals($entry['key'], $key))
                continue;

            $this->detach();

            $this->state->data()['buckets'][$hash][$index]['value'] = $value;

            return MutationOutcome::UPDATED;

        }

        return MutationOutcome::NOT_FOUND;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To hash the key.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the key.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::detach() To detach the engine state.
     * @uses \FireHub\Runtime\Arr\Structure::splice() To splice the engine state.
     */
    public function remove (mixed $key):MutationOutcome {

        $hash = $this->strategy->hash($key);

        if (!isset($this->state->data()['buckets'][$hash]))
            return MutationOutcome::NOT_FOUND;

        foreach ($this->state->data()['buckets'][$hash] as $index => $entry) {

            if (!$this->strategy->equals($entry['key'], $key))
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

            foreach ($data['order'] as $position => $ordered_key) {

                if (!$this->strategy->equals($ordered_key, $entry['key']))
                    continue;

                Runtime\Arr\Structure::splice(
                    $data['order'],
                    $position,
                    1
                );

                break;

            }

            $data['size']--;

            return MutationOutcome::REMOVED;

        }

        return MutationOutcome::NOT_FOUND;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     */
    public function map (callable $callback):self {

        $data = $this->state->data();

        foreach ($data['buckets'] as &$bucket)
            foreach ($bucket as &$entry)
                $entry['value'] = $callback(
                    $entry['value'],
                    $entry['key']
                );

        unset($bucket, $entry);

        return clone($this, [
            'state' => new SharedState($data)
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::emptyCopy() To create an empty copy.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::iterate() To iterate over the engine.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::set() To set an entry.
     */
    public function filter (callable $callback):self {

        $result = $this->emptyCopy();

        foreach ($this->iterate() as $key => $value)
            if ($callback($value, $key))
                $result->set($key, $value);

        return $result;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::entries() To get entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::fromEntries() To create a hash from
     * entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::sortBy() To sort entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::sortEntriesUsing() To sort entries using
     * the specified algorithm.
     */
    public function sort (Order $order = Order::ASC, ?SortAlgorithm $algorithm = null):self {

        $entries = $this->entries();

        $comparator = match ($order) {
            Order::ASC => static fn (array $first, array $second):int =>
                $first['value'] <=> $second['value'], // @phpstan-ignore-line

            Order::DESC => static fn (array $first, array $second):int =>
                $second['value'] <=> $first['value'] // @phpstan-ignore-line
        };

        if ($algorithm !== null)
            // @phpstan-ignore-next-line
            $this->sortEntriesUsing($entries, $algorithm, $comparator);
        else
            Runtime\Arr\Ordering::sortBy($entries, $comparator);

        return $this->fromEntries($entries);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::entries() To get entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::fromEntries() To create a hash from
     * entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::sortBy() To sort entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::sortEntriesUsing() To sort entries using
     * the specified algorithm.
     * @uses \FireHub\Runtime\Arr\Ordering::sortBy() To sort entries using the specified algorithm.
     */
    public function sortKeys (Order $order = Order::ASC, ?SortAlgorithm $algorithm = null):self {

        $entries = $this->entries();

        $comparator = match ($order) {
            Order::ASC => static fn (array $first, array $second):int =>
                $first['key'] <=> $second['key'], // @phpstan-ignore-line

            Order::DESC => static fn (array $first, array $second):int =>
                $second['key'] <=> $first['key'] // @phpstan-ignore-line
        };

        if ($algorithm !== null)
            // @phpstan-ignore-next-line
            $this->sortEntriesUsing($entries, $algorithm, $comparator);
        else
            Runtime\Arr\Ordering::sortBy($entries, $comparator);

        return $this->fromEntries($entries);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::entries() To get entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::fromEntries() To create a hash from
     * entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::sortBy() To sort entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::sortEntriesUsing() To sort entries using
     * the specified algorithm.
     */
    public function sortWith (callable $comparator, ?SortAlgorithm $algorithm = null):self {

        $entries = $this->entries();

        $entry_comparator = static fn (array $first, array $second):int =>
        $comparator(
            $first['value'], // @phpstan-ignore offsetAccess.notFound
            $second['value'] // @phpstan-ignore offsetAccess.notFound
        );

        if ($algorithm !== null)
            $this->sortEntriesUsing(
                $entries,
                $algorithm, // @phpstan-ignore argument.type
                $entry_comparator
            );
        else
            Runtime\Arr\Ordering::sortBy(
                $entries,
                $entry_comparator
            );

        return $this->fromEntries($entries);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::entries() To get entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::fromEntries() To create a hash from
     * entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::sortBy() To sort entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::sortEntriesUsing() To sort entries using
     * the specified algorithm.
     */
    public function sortKeysWith (callable $comparator, ?SortAlgorithm $algorithm = null):self {

        $entries = $this->entries();

        $entry_comparator = static fn (array $first, array $second):int =>
        $comparator(
            $first['key'], // @phpstan-ignore offsetAccess.notFound
            $second['key'] // @phpstan-ignore offsetAccess.notFound
        );

        if ($algorithm !== null)
            $this->sortEntriesUsing(
                $entries,
                $algorithm, // @phpstan-ignore argument.type
                $entry_comparator
            );
        else
            Runtime\Arr\Ordering::sortBy(
                $entries,
                $entry_comparator
            );

        return $this->fromEntries($entries);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::entries() To get entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::fromEntries() To create a hash from
     * entries.
     * @uses \FireHub\Runtime\Arr\Structure::slice() To slice the entries.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidRangeLength If the range length is less
     * than zero.
     */
    public function slice (int $offset, ?int $length = null):self {

        if ($length !== null && $length < 0)
            throw new InvalidRangeLength(
                'Range length must be greater than or equal to zero.'
            );

        /** @var list<array{key: TKey, value: TValue}> $entries */
        $entries = Runtime\Arr\Structure::slice(
            $this->entries(),
            $offset,
            $length
        );

        return $this->fromEntries($entries);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::entries() To get entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::fromEntries() To create a hash from
     * entries.
     * @uses \FireHub\Runtime\Arr\Transform::reverse() To reverse the entries.
     */
    public function reverse ():self {

        /** @var list<array{key: TKey, value: TValue}> $entries */
        $entries = Runtime\Arr\Transform::reverse(
            $this->entries()
        );

        return $this->fromEntries($entries);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::entries() To get entries.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::fromEntries() To create a hash from
     * entries.
     * @uses \FireHub\Runtime\Arr\Ordering::shuffle() To shuffle the entries.
     */
    public function shuffle ():self {

        $entries = $this->entries();

        Runtime\Arr\Ordering::shuffle($entries);

        return $this->fromEntries($entries);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::initializeEntry() To merge an entry.
     */
    public function merge (iterable $values):self {

        $data = $this->state->data();

        foreach ($values as $key => $value)
            $this->initializeEntry(
                $data, // @phpstan-ignore argument.type
                $key,
                $value
            );

        return clone($this, [ // @phpstan-ignore assign.propertyType
            'state' => new SharedState($data)
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::findEntry() To find an entry.
     */
    public function reduce (mixed $initial, callable $callback):mixed {

        $carry = $initial;

        foreach ($this->state->data()['order'] as $key) {

            $entry = $this->findEntry($key);

            if ($entry !== null)
                $carry = $callback($carry, $entry['value']);

        }

        return $carry;

    }

    /**
     * ### Finds an entry for the specified key
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy\HashStrategy::hash() To hash the key.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy\EqualityStrategy::equals() To compare the key.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     *
     * @param TKey $key <p>
     * Key whose entry should be located.
     * </p>
     *
     * @return null|array{key: TKey, value: TValue} The matching entry, or null if no matching entry exists.
     */
    private function findEntry (mixed $key):?array {

        $hash = $this->strategy->hash($key);

        foreach ($this->state->data()['buckets'][$hash] ?? [] as $entry)
            if ($this->strategy->equals($entry['key'], $key))
                return $entry;

        return null;

    }

    /**
     * ### Gets entries in their logical order
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the engine state.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::findEntry() To find an entry.
     *
     * @return list<array{key: TKey, value: TValue}> Ordered entries.
     */
    private function entries ():array {

        $entries = [];

        foreach ($this->state->data()['order'] as $key) {

            $entry = $this->findEntry($key);

            if ($entry !== null)
                $entries[] = $entry;

        }

        return $entries;

    }

    /**
     * ### Creates a bucket hash from ordered entries
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::emptyCopy() To create an empty copy.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash::set() To set an entry.
     *
     * @param list<array{key: TKey, value: TValue}> $entries <p>
     * Entries used to create the hash.
     * </p>
     *
     * @return self<TKey, TValue> Bucket hash containing the specified entries.
     */
    private function fromEntries (array $entries):self {

        $result = $this->emptyCopy();

        foreach ($entries as $entry)
            $result->set(
                $entry['key'],
                $entry['value']
            );

        return $result;

    }

    /**
     * ### Initializes an entry
     *
     * Adds or updates an entry directly within the specified state without invoking copy-on-write behavior.
     *
     * This method is intended exclusively for construction-time initialization.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::hash() To hash the key.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Strategy::equals() To compare the key.
     *
     * @param State $state <p>
     * State being initialized.
     * </p>
     * @param TKey $key <p>
     * Key to initialize.
     * </p>
     * @param TValue $value <p>
     * Value associated with the key.
     * </p>
     * @phpstan-param-out array{
     *     buckets: non-empty-array<string, non-empty-list<array{key?: TKey, value: TValue}>>,
     *     order: list<TKey>,size: int
     * } $state
     *
     * @return void
     */
    private function initializeEntry (array &$state, mixed $key, mixed $value):void {

        $hash = $this->strategy->hash($key);

        foreach ($state['buckets'][$hash] ?? [] as $index => $entry) {

            if (!$this->strategy->equals($entry['key'], $key))
                continue;

            $state['buckets'][$hash][$index]['value'] = $value;

            return;

        }

        $state['buckets'][$hash][] = [
            'key' => $key,
            'value' => $value
        ];

        $state['order'][] = $key;
        $state['size']++;

    }

    /**
     * ### Sorts entries using a sorting algorithm
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm::sort() To sort the entries.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of entries.
     *
     * @param list<array{key: TKey, value: TValue}> $entries <p>
     * Entries to sort.
     * </p>
     * @param \FireHub\Core\Boundary\Algorithm\Sorting\SortAlgorithm<array{key: TKey, value: TValue}> $algorithm <p>
     * Sorting algorithm.
     * </p>
     * @param callable(array{key: TKey, value: TValue}, array{key: TKey, value: TValue}):int $comparator <p>
     * Comparator used to order entries.
     * </p>
     *
     * @return void
     */
    private function sortEntriesUsing (array &$entries, SortAlgorithm $algorithm, callable $comparator):void {

        $algorithm->sort(
            Runtime\Arr\Inspection::count($entries),
            static function (int $index) use (&$entries):mixed {

                return $entries[$index]; // @phpstan-ignore offsetAccess.notFound

            },
            static function (int $index, mixed $entry) use (&$entries):void {

                $entries[$index] = $entry;

            },
            static function (int $first, int $second) use (&$entries):void {

                $temporary = $entries[$first]; // @phpstan-ignore offsetAccess.notFound

                $entries[$first] = $entries[$second]; // @phpstan-ignore offsetAccess.notFound
                $entries[$second] = $temporary;

            },
            $comparator
        );

    }

}