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
use Generator;

/**
 * ### Provides an initialization strategy based on a repeated value
 *
 * Fill initializer generates a defined number of elements containing the same value. The generated elements are
 * supplied as the initial contents of a storage while preserving their sequential order.
 *
 * The initializer defines how the initial values are generated but does not determine how those values are
 * represented, stored, accessed, or managed by the consuming storage.
 * @since 1.0.0
 *
 * @template TKey of int
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Initializer<TKey, TValue>
 */
final readonly class FillInit implements Initializer {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param int $length <p>
     * The number of elements to generate.
     * </p>
     * @param TValue $value <p>
     * The value to repeat.
     * </p>
     *
     * @return void
     */
    public function __construct(
        private int $length,
        private mixed $value
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return Generator<TKey, TValue> The generator of values to use as the initial data for the storage.
     */
    public function initialize ():Generator {

        for ($key = 0; $key < $this->length; $key++)
            /** @var TKey $key */
            yield $key => $this->value;

    }

}