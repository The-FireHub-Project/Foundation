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

/**
 * ### Provides an initialization strategy based on an array of values
 *
 * Array initializer supplies the contents of an array as the initial data for a storage. It allows an existing
 * array to be used as the source of initial values while leaving the storage implementation responsible for
 * organizing and materializing those values according to its own storage semantics.
 *
 * The initializer does not impose the array's internal representation on the consuming storage and can therefore
 * be used with any storage implementation that accepts the supplied key and value types.
 * @since 1.0.0
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Initializer<TKey, TValue>
 */
final readonly class ArrayInit implements Initializer {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param array<TKey, TValue> $values <p>
     * The array of values to use as the initial data for the storage.
     * </p>
     *
     * @return void
     */
    public function __construct(
        private array $values
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array<TKey, TValue> The array of values to use as the initial data for the storage.
     */
    public function initialize ():array {

        return $this->values;

    }

}