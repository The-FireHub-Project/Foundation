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
    Cloneable, Forkable
};
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Exception\InvalidOccurrencesException;
use FireHub\Foundation\DataStructure\Storage\Hash\Engine;

/**
 * ### Provides a storage implementation for hash-based value multiplicities
 *
 * Hash bag storage maintains values together with the number of times each logically distinct value occurs.
 *
 * Each distinct bag value is represented as a key within the underlying hash engine, while the associated engine
 * value represents the number of occurrences of that value within the bag.
 *
 * The underlying hash engine defines how values are stored and resolved. Array-backed engines may provide native
 * PHP array-key lookup for supported values, while bucket-based engines may provide hashing and equality semantics
 * for arbitrary value types.
 *
 * Hash bag storage adapts key-value hash engine operations to multiplicity-oriented storage semantics. The number
 * of distinct values is provided by the underlying hash engine, while the total number of stored occurrences is
 * tracked independently by the storage.
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
 */
final class HashBagStorage implements Storage, Cloneable, Forkable, DistinctMetrics, MultiplicityAccess,
    MultiplicityMutation {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\Hash\Engine<TValue, positive-int> $engine <p>
     * The hash engine used to store distinct bag values together with their occurrence counts.
     * </p>
     * @param non-negative-int $size [optional] <p>
     * The total number of occurrences represented by the hash engine.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private readonly Engine $engine,
        private int $size = 0
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
            $this->engine->copy(),
            $this->size
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
            $this->engine->fork(),
            $this->size
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

        foreach ($this->engine->iterate() as $value => $count)
            for ($occurrence = 0; $occurrence < $count; $occurrence++)
                yield $index++ => $value;

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

        return $this->size;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::size() To get the size of the hash engine.
     */
    public function distinctSize ():int {

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
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::get() To get the number of occurrences associated
     * with the specified value.
     * @uses \FireHub\Core\Type\Maybe::isSome() To check if the value is present in the hash engine.
     * @uses \FireHub\Core\Type\Maybe::value() To get the number of occurrences associated with the value.
     */
    public function frequency (mixed $value):int {

        $frequency = $this->engine->get($value);

        /** @var non-negative-int */
        return $frequency->isSome()
            ? $frequency->value()
            : 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::set() To set the number of occurrences associated
     * with the specified value.
     * @uses \FireHub\Core\Type\Maybe::isSome() To check if the value is already present in the hash engine.
     * @uses \FireHub\Core\Type\Maybe::value() To get the number of occurrences associated with the value.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidOccurrencesException If the specified number
     * of occurrences is less than or equal to zero.
     */
    public function add (mixed $value, int $count = 1):MutationOutcome {

        if ($count < 1)
            throw new InvalidOccurrencesException('The number of occurrences must be greater than zero.');

        $frequency = $this->engine->get($value);

        if ($frequency->isSome()) {

            /** @var non-negative-int $frequency_value */
            $frequency_value = $frequency->value();

            $this->engine->set(
                $value,
                $frequency_value + $count
            );

            $this->size += $count;

            return MutationOutcome::UPDATED;

        }

        $this->engine->set($value, $count);

        $this->size += $count;

        return MutationOutcome::CREATED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::remove() To remove the specified value from the
     * hash engine.
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::get() To get the number of occurrences associated
     * with the specified value.
     * @uses \FireHub\Core\Type\Maybe::isNone() To check if the value is not present in the hash engine.
     * @uses \FireHub\Core\Type\Maybe::value() To get the number of occurrences associated with the value.
     *
     * @throws \FireHub\Foundation\DataStructure\Storage\Exception\InvalidOccurrencesException If the specified number
     * of occurrences is less than or equal to zero.
     */
    public function remove (mixed $value, int $count = 1):MutationOutcome {

        if ($count < 1)
            throw new InvalidOccurrencesException('The number of occurrences must be greater than zero.');

        $frequency = $this->engine->get($value);

        if ($frequency->isNone())
            return MutationOutcome::NOT_FOUND;

        /** @var positive-int $current */
        $current = $frequency->value();

        if ($current > $count) {

            /** @var positive-int $current_count */
            $current_count = $current - $count;

            $this->engine->set(
                $value,
                $current_count
            );

            $this->size -= $count; // @phpstan-ignore assign.propertyType

            return MutationOutcome::UPDATED;

        }

        $this->engine->remove($value);

        $this->size -= $current; // @phpstan-ignore assign.propertyType

        return MutationOutcome::REMOVED;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Hash\Engine::remove() To remove all occurrences of the specified
     * value from the hash engine.
     * @uses \FireHub\Core\Type\Maybe::isNone() To check if the value is not present in the hash engine.
     * @uses \FireHub\Core\Type\Maybe::value() To get the number of occurrences associated with the value.
     */
    public function removeAll (mixed $value):MutationOutcome {

        $frequency = $this->engine->get($value);

        if ($frequency->isNone())
            return MutationOutcome::NOT_FOUND;

        $this->engine->remove($value);

        $this->size -= $frequency->value(); // @phpstan-ignore-line

        return MutationOutcome::REMOVED;

    }

}