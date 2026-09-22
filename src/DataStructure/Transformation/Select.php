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

namespace FireHub\Foundation\DataStructure\Transformation;

use FireHub\Core\Boundary\Capability\Transformation\Filterable;
use FireHub\Foundation\DataStructure\Exception\SelectIntervalException;

/**
 * ### Select transformation
 *
 * Provides higher-level operations for selecting values from a data structure according to their position within
 * its canonical iteration sequence.
 *
 * Unlike take and skip transformations, which operate on a consecutive prefix of a data structure, select
 * transformations evaluate positions throughout the entire iteration sequence and retain only values occupying
 * positions that satisfy the requested positional strategy.
 *
 * Positions are one-based and independent of the keys exposed by the source data structure. The first iterated
 * value occupies position 1, the second occupies position 2, and so on. This allows positional selection to behave
 * consistently across sequential and associative data structures regardless of their key types.
 *
 * Select delegates value retention to the underlying Filterable data structure, allowing the source to preserve its
 * concrete type, keys, ordering semantics, and applicable configuration when constructing the result.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 * @template TSource of \FireHub\Core\Boundary\Capability\Transformation\Filterable<TKey, TValue>
 */
final readonly class Select {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TSource $source <p>
     * The filterable data structure used as the source for positional selection transformations.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Filterable $source
    ) {}

    /**
     * ### Selects every nth value
     *
     * Selects values occupying every nth position within the canonical iteration sequence of the source data
     * structure.
     *
     * Positions are counted from one. Therefore, an interval of 3 selects values at positions 3, 6, 9, and so on.
     * The original keys associated with selected values are preserved according to the filtering semantics of the
     * underlying data structure.
     *
     * Every value in the source is considered when determining its position, including values that are not selected.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Filterable::filter() To select values at matching
     * positions.
     *
     * @param positive-int $interval <p>
     * Interval between selected positions. A value of 1 selects every value, 2 selects every second value, 3 selects
     * every third value, and so on.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\SelectIntervalException If interval is less than one.
     *
     * @return TSource The resulting data structure containing values occupying every nth position.
     */
    public function every (int $interval):Filterable {

        if ($interval < 1)
            throw new SelectIntervalException(
                'The selection interval must be greater than zero.'
            );

        $position = 1;

        return $this->source->filter(
            static function () use (&$position, $interval):bool {

                return $position++ % $interval === 0;

            }
        );

    }

    /**
     * ### Selects values at even positions
     *
     * Selects values occupying even positions within the canonical iteration sequence of the source data structure.
     *
     * Positions are counted from one. Therefore, this operation selects values at positions 2, 4, 6, 8, and so on,
     * regardless of the keys associated with those values.
     *
     * Every value in the source is considered when determining its position, including values that are not selected.
     * The original keys associated with selected values are preserved according to the filtering semantics of the
     * underlying data structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Filterable::filter() To select values at even positions.
     *
     * @return TSource The resulting data structure containing values occupying even positions.
     */
    public function even ():Filterable {

        $position = 1;

        return $this->source->filter(
            static function () use (&$position):bool {

                return $position++ % 2 === 0;

            }
        );

    }

    /**
     * ### Selects values at odd positions
     *
     * Selects values occupying odd positions within the canonical iteration sequence of the source data structure.
     *
     * Positions are counted from one. Therefore, this operation selects values at positions 1, 3, 5, 7, and so on,
     * regardless of the keys associated with those values.
     *
     * Every value in the source is considered when determining its position, including values that are not selected.
     * The original keys associated with selected values are preserved according to the filtering semantics of the
     * underlying data structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Capability\Transformation\Filterable::filter() To select values at odd positions.
     *
     * @return TSource The resulting data structure containing values occupying odd positions.
     */
    public function odd ():Filterable {

        $position = 1;

        return $this->source->filter(
            static function () use (&$position):bool {

                return $position++ % 2 !== 0;

            }
        );

    }

}