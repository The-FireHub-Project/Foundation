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

namespace FireHub\Foundation\DataStructure\Transformation;

use FireHub\Core\Boundary\Type\Enumerable;
use FireHub\Core\Boundary\Capability\Transformation\Filterable;
use FireHub\Foundation\DataStructure\Bag;
use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\MixedHashStrategy;
use FireHub\Foundation\DataStructure\Aggregation\Count;

/**
 * ### Multiplicity transformation
 *
 * Provides higher-level transformations for selecting values according to their multiplicity within the canonical
 * iteration sequence of a data structure.
 *
 * Multiplicity transformations preserve the original values while determining distinctness, uniqueness, or
 * duplication either directly from those values or from identities derived from them.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 * @template TSource of \FireHub\Core\Boundary\Type\Enumerable<TKey, TValue>&\FireHub\Core\Boundary\Capability\Transformation\Filterable<TKey, TValue>
 */
final readonly class Multiplicity {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TSource $source <p>
     * The filterable data structure used as the source for multiplicity transformations.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Enumerable&Filterable $source
    ) {}

    /**
     * ### Selects distinct values
     *
     * Selects the first occurrence of every distinct value contained within the canonical iteration sequence of the
     * source data structure.
     *
     * Subsequent occurrences of logically equal values are excluded from the resulting data structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Transformation\Multiplicity::distinctBy() To select distinct values by
     * derived identity.
     *
     * @return TSource A data structure containing the first occurrence of every distinct value.
     */
    public function distinct ():Filterable {

        return $this->distinctBy(
            static fn (mixed $value):mixed => $value
        );

    }

    /**
     * ### Selects distinct values by derived identity
     *
     * Selects the first source value for every distinct identity derived from the canonical iteration sequence of the
     * source data structure.
     *
     * The selector derives the identity used to determine distinctness while the original source values are preserved
     * in the resulting data structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Bag::add() To add distinct identities to the bag.
     * @uses \FireHub\Foundation\DataStructure\Bag::contains() To check if an identity is contained in the bag.
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Filterable::filter() To filter the source data
     * structure based on the bag's membership.
     *
     * @template TIdentity
     *
     * @param callable(TValue, TKey=):TIdentity $selector <p>
     * Callback that derives the identity used to determine distinctness.
     * </p>
     *
     * @return TSource A data structure containing the first source value for every distinct derived identity.
     */
    public function distinctBy (callable $selector):Filterable {

        /** @var Bag<TIdentity> $seen */
        $seen = new Bag(new HashBagStorage(new MixedHashStrategy));

        return $this->source->filter(
            static function (mixed $value, mixed $key = null) use ($selector, &$seen):bool {

                $identity = $selector($value, $key);

                if ($seen->contains($identity)) return false;

                $seen->add($identity);

                return true;

            }
        );

    }

    /**
     * ### Selects unique values
     *
     * Selects values that occur exactly once within the canonical iteration sequence of the source data structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Transformation\Multiplicity::uniqueBy() To select unique values by
     * derived identity.
     *
     * @return TSource A data structure containing only values that occur exactly once.
     */
    public function unique ():Filterable {

        return $this->uniqueBy(
            static fn (mixed $value):mixed => $value
        );

    }

    /**
     * ### Selects unique values by derived identity
     *
     * Selects source values whose derived identity occurs exactly once within the canonical iteration sequence of the
     * source data structure.
     *
     * The selector derives the identity used to determine multiplicity while the original source values are preserved
     * in the resulting data structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Aggregation\Count::by() To count occurrences of derived identities.
     * @uses \FireHub\Foundation\DataStructure\Bag::frequency() To count occurrences of derived identities.
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Filterable::filter() To filter the source data
     * structure based on the count of occurrences.
     *
     * @template TIdentity
     *
     * @param callable(TValue, TKey=):TIdentity $selector <p>
     * Callback that derives the identity used to determine multiplicity.
     * </p>
     *
     * @return TSource A data structure containing source values whose derived identity occurs exactly once.
     */
    public function uniqueBy (callable $selector):Filterable {

        $counts = new Count($this->source)->by($selector);

        return $this->source->filter(
            static fn (mixed $value, mixed $key = null):bool =>
                $counts->frequency($selector($value, $key)) === 1
        );

    }

    /**
     * ### Selects duplicate values
     *
     * Selects the first occurrence of every value that occurs more than once within the canonical iteration sequence
     * of the source data structure.
     *
     * Each duplicated value is represented exactly once in the resulting data structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Transformation\Multiplicity::duplicatesBy() To select duplicate values
     * by derived identity.
     *
     * @return TSource A data structure containing the first occurrence of every duplicated value.
     */
    public function duplicates ():Filterable {

        return $this->duplicatesBy(
            static fn (mixed $value):mixed => $value
        );

    }

    /**
     * ### Selects duplicate values by derived identity
     *
     * Selects the first source value for every derived identity that occurs more than once within the canonical
     * iteration sequence of the source data structure.
     *
     * The selector derives the identity used to determine multiplicity while the original source values are preserved
     * in the resulting data structure. Each duplicated identity is represented by its first corresponding source
     * value.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Aggregation\Count::by() To count occurrences of derived identities.
     * @uses \FireHub\Foundation\DataStructure\Bag::frequency() To count occurrences of derived identities.
     * @uses \FireHub\Foundation\DataStructure\Bag::add() To add duplicated identities to the bag.
     * @uses \FireHub\Foundation\DataStructure\Bag::contains() To check if an identity is contained in the bag.
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Filterable::filter() To filter the source data
     * structure based on the count of occurrences.
     *
     * @template TIdentity
     *
     * @param callable(TValue, TKey=):TIdentity $selector <p>
     * Callback that derives the identity used to determine multiplicity.
     * </p>
     *
     * @return TSource A data structure containing the first source value for every duplicated derived identity.
     */
    public function duplicatesBy (callable $selector):Filterable {

        $counts = new Count($this->source)->by($selector);

        /** @var Bag<TIdentity> $selected */
        $selected = new Bag(new HashBagStorage(new MixedHashStrategy));

        return $this->source->filter(
            static function (mixed $value, mixed $key = null) use ($selector, $counts, &$selected):bool {

                $identity = $selector($value, $key);

                if ($counts->frequency($identity) <= 1) return false;

                if ($selected->contains($identity)) return false;

                $selected->add($identity);

                return true;

            }
        );

    }

}