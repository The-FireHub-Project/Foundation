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

namespace FireHub\Foundation\DataStructure\Storage\Initialization;

use FireHub\Foundation\DataStructure\Storage\Initializer;
use FireHub\Runtime;

/**
 * ### Provides an initialization strategy based on a repeated value
 *
 * ArrFill initializer generates the initial contents of a storage by filling a defined number of positions with the
 * same value. The supplied value is repeated for each position while preserving the sequential order of the
 * generated contents.
 *
 * The initializer defines how the initial values are generated but does not determine how those values are
 * represented, stored, accessed, or managed by the consuming storage.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Initializer<int, TValue>
 */
final readonly class ArrFill implements Initializer {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * Value to use for filling.
     * </p>
     * @param int $start_index <p>
     * The first index of the returned array.
     * </p>
     * @param positive-int $length <p>
     * Number of elements to insert. Must be greater than or equal to zero.
     * </p>
     *
     * @return void
     */
    public function __construct(
        private mixed $value,
        private int $start_index,
        private int $length
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Structure::fill() To generate the filled array.
     *
     * @throws \FireHub\Runtime\Exception\InvalidArrayLengthException If $length is invalid.
     */
    public function initialize ():iterable {

        return Runtime\Arr\Structure::fill($this->value, $this->start_index, $this->length);

    }

}