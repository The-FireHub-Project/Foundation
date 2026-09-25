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

namespace FireHub\Foundation\DataStructure\Aggregation;

use FireHub\Core\Boundary\Type\Enumerable;
use FireHub\Foundation\DataStructure\Bag;
use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\MixedHashStrategy;

/**
 * ### Count aggregation
 *
 * Provides higher-level aggregation operations for counting values within a data structure according to predicates,
 * values, or identities derived from values within its canonical iteration sequence.
 *
 * Count does not modify the source data structure. Operations either return a scalar count or construct a Bag
 * preserving the multiplicity of the counted values or derived identities.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
final readonly class Count {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\Type\Enumerable<TKey, TValue> $source <p>
     * The enumerable data structure used as the source for counting operations.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Enumerable $source
    ) {}

    /**
     * ### Counts values matching a predicate
     *
     * Counts values for which the predicate evaluates to true within the canonical iteration sequence of the source
     * data structure.
     *
     * The predicate receives the original value and key associated with that value.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey=):bool $predicate <p>
     * Callback used to determine whether a value should be counted.
     * </p>
     *
     * @return non-negative-int The number of values satisfying the predicate.
     */
    public function where (callable $predicate):int {

        $count = 0;

        foreach ($this->source as $key => $value)
            if ($predicate($value, $key))
                $count++;

        return $count;

    }

    /**
     * ### Counts occurrences of values
     *
     * Counts the occurrences of values contained within the canonical iteration sequence of the source data structure.
     *
     * Each source value is added to the resulting Bag, preserving the number of times each logically equal value
     * occurs.
     * The multiplicity of a value therefore represents its number of occurrences within the source.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Bag<mixed> A bag containing the source values and preserving their
     * multiplicities.
     */
    public function values ():Bag {

        return $this->by(
            static fn (mixed $value):mixed => $value
        );

    }

    /**
     * ### Counts occurrences by derived identity
     *
     * Counts the occurrences of identities derived from values contained within the canonical iteration sequence of the
     * source data structure.
     *
     * The selector receives the original value and key and is evaluated once for every source element. Each derived
     * identity is added to the resulting Bag, with its multiplicity representing the number of source elements that
     * produced a logically equal identity.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Bag::add() To add derived identities to the bag.
     *
     * @template TIdentity
     *
     * @param callable(TValue, TKey=):TIdentity $selector <p>
     * Callback that derives the identity used to group and count values.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Bag<TIdentity> A bag containing the derived identities and preserving
     * their multiplicities.
     */
    public function by (callable $selector):Bag {

        $bag = new Bag(new HashBagStorage(new MixedHashStrategy));

        foreach ($this->source as $key => $value)
            $bag->add($selector($value, $key));

        /** @var Bag<TIdentity> */
        return $bag;

    }

}