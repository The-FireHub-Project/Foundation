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

use FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable;
use FireHub\Foundation\DataStructure\Exception\TakeSizeException;

/**
 * ### Take transformation
 *
 * Provides higher-level operations for taking consecutive elements from the beginning of a data structure.
 *
 * Take delegates the primitive conditional operation to the underlying Takeable data structure while providing
 * convenient strategies such as fixed-size and condition-based taking.
 *
 * The source data structure remains responsible for constructing the result, allowing the resulting data structure
 * to preserve the concrete type and semantics of its source.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 * @template TSource of \FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable<TKey, TValue>
 */
final readonly class Take {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TSource $source <p>
     * The takeable data structure used as the source for take transformations.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Takeable $source
    ) {}

    /**
     * ### Takes first elements
     *
     * Takes at most the specified number of elements from the beginning of the source.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable::takeWhile() To take values from the
     * source.
     *
     * @param non-negative-int $count <p>
     * Maximum number of elements to take.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\TakeSizeException If count is less than zero.
     *
     * @return TSource The resulting data structure.
     */
    public function first (int $count):Takeable {

        if ($count < 0)
            throw new TakeSizeException(
                'The number of elements to take cannot be less than zero.'
            );

        $position = 0;

        return $this->source->takeWhile(
            static function () use (&$position, $count):bool {

                return $position++ < $count;

            }
        );

    }

    /**
     * ### Takes while condition is satisfied
     *
     * Takes consecutive elements from the beginning of the source while the callback evaluates to true.
     *
     * Taking terminates when the callback first evaluates to false. The element for which the callback evaluates to
     * false is not included in the result.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable::takeWhile() To take values from the
     * source.
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines whether the current element should be included in the result.
     * </p>
     *
     * @return TSource The resulting data structure.
     */
    public function while (callable $callback):Takeable {

        return $this->source->takeWhile($callback);

    }

    /**
     * ### Takes values until a condition is satisfied
     *
     * Takes consecutive values from the beginning of the source until the supplied callback evaluates to true.
     *
     * The callback is evaluated sequentially according to the canonical iteration order of the source. Taking continues
     * while the callback evaluates to false and terminates immediately when the callback first evaluates to true.
     *
     * The value for which the callback first evaluates to true is not included in the result, and no later values
     * are evaluated.
     *
     * This operation is the logical inverse of {@see self::while()} with respect to the supplied predicate and is
     * implemented by negating the callback result before delegating to the underlying take operation.
     *
     * If the callback evaluates to true for the first value, an empty data structure is returned. If the callback never
     * evaluates to true, the result contains all values from the source data structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable::takeWhile() To take values until the
     * condition is satisfied.
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines when taking should stop. The callback receives the current value and its corresponding
     * key and must return true to stop taking, or false to continue taking values.
     * </p>
     *
     * @return TSource The resulting data structure containing the consecutive values preceding the first value for which
     * the callback evaluates to true.
     */
    public function until (callable $callback):Takeable {

        return $this->source->takeWhile(
            static fn ($value, $key = null):bool => !$callback($value, $key)
        );

    }

}