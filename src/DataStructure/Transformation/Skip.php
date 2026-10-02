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

use FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable;
use FireHub\Foundation\DataStructure\Exception\SkipSizeException;

/**
 * ### Skip transformation
 *
 * Provides higher-level operations for skipping consecutive elements from the beginning of a data structure.
 *
 * Skip delegates the primitive conditional operation to the underlying Skippable data structure while providing
 * convenient strategies such as fixed-size and condition-based skipping.
 *
 * The source data structure remains responsible for constructing the result, allowing the resulting data structure
 * to preserve the concrete type and semantics of its source.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 * @template TSource of \FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable<TKey, TValue>
 */
final readonly class Skip {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TSource $source <p>
     * The skippable data structure used as the source for skip transformations.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Skippable $source
    ) {}

    /**
     * ### Skips first elements
     *
     * Skips at most the specified number of elements from the beginning of the source.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable::skipWhile() To skip values from the
     * source.
     *
     * @param non-negative-int $count <p>
     * Maximum number of elements to skip.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\SkipSizeException If count is less than zero.
     *
     * @return TSource The resulting data structure.
     */
    public function first (int $count):Skippable {

        if ($count < 0)
            throw new SkipSizeException(
                'The number of elements to skip cannot be less than zero.'
            );

        $position = 0;

        return $this->source->skipWhile(
            static function () use (&$position, $count):bool {

                return $position++ < $count;

            }
        );

    }

    /**
     * ### Skips while condition is satisfied
     *
     * Skips consecutive elements from the beginning of the source while the callback evaluates to true.
     *
     * Skipping terminates when the callback first evaluates to false. The element for which the callback evaluates to
     * false becomes the first element of the result.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable::skipWhile() To skip values from the
     * source.
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines whether the current element should continue to be skipped.
     * </p>
     *
     * @return TSource The resulting data structure.
     */
    public function while (callable $callback):Skippable {

        return $this->source->skipWhile($callback);

    }

    /**
     * ### Skips values until a condition is satisfied
     *
     * Skips consecutive values from the beginning of the source until the supplied callback evaluates to true.
     *
     * The callback is evaluated sequentially according to the canonical iteration order of the source. Skipping
     * continues while the callback evaluates to false and terminates immediately when the callback first evaluates to
     * true.
     *
     * The value for which the callback first evaluates to true is not skipped and becomes the first value included in
     * the result. All later values are included without further callback evaluation.
     *
     * This operation is the logical inverse of {@see self::while()} with respect to the supplied predicate and is
     * implemented by negating the callback result before delegating to the underlying skip operation.
     *
     * If the callback evaluates to true for the first value, no values are skipped and the result contains the entire
     * source data structure. If the callback never evaluates to true, an empty data structure is returned.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable::skipWhile() To skip values until the
     * condition is satisfied.
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines when skipping should stop. The callback receives the current value and its corresponding
     * key and must return true to stop skipping, or false to continue skipping values.
     * </p>
     *
     * @return TSource The resulting data structure beginning with the first value for which the callback evaluates to
     * true.
     */
    public function until (callable $callback):Skippable {

        return $this->source->skipWhile(
            static fn ($value, $key = null):bool => !$callback($value, $key)
        );

    }

}