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

use FireHub\Foundation\DataStructure\Boundary\Transformation\Splittable;
use FireHub\Foundation\DataStructure\Stream;

/**
 * ### Split transformation
 *
 * Provides higher-level operations for partitioning a data structure into consecutive segments separated by
 * matching elements.
 *
 * Split delegates the primitive splitting operation to the underlying Splittable data structure while providing
 * convenient strategies for determining separators.
 *
 * Separator elements are excluded from the generated segments.
 *
 * The source data structure remains responsible for constructing individual segments, allowing each generated
 * segment to preserve the concrete type and semantics of its source.
 *
 * Splitting is performed lazily by the underlying data structure, with generated segments exposed through a Stream
 * as they are consumed.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 * @template TSource of \FireHub\Foundation\DataStructure\Boundary\Transformation\Splittable
 */
final readonly class Split {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TSource $source <p>
     * The splittable data structure used as the source for split transformations.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Splittable $source
    ) {}

    /**
     * ### Splits by condition
     *
     * Splits the source whenever the specified callback evaluates to true.
     *
     * Matching elements act as separators and are excluded from the generated segments.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Transformation\Splittable::splitBy() To partition the source.
     *
     * @param callable(TValue, TKey=):bool $callback <p>
     * Callback that determines whether the current element acts as a separator.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, TSource> The generated segments.
     */
    public function where (callable $callback):Stream {

        return $this->source->splitBy($callback);

    }

    /**
     * ### Splits by value
     *
     * Splits the source whenever an element strictly equals the specified separator.
     *
     * Matching separator elements are excluded from the generated segments.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Transformation\Split::where() To split the source by a condition.
     *
     * @param TValue $separator <p>
     * Value used as the separator.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, TSource> The generated segments.
     */
    public function byValue (mixed $separator):Stream {

        return $this->where(
            static fn (mixed $value):bool => $value === $separator
        );

    }

}