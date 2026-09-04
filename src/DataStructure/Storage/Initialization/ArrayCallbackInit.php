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
use Closure;

/**
 * ### Provides an initialization strategy based on a callback
 *
 * ArrCallback initializer generates the initial
 * contents of a storage by invoking a user-defined callback for each requested value. The callback determines the
 * value produced at each position, allowing the initial contents to be generated dynamically rather than supplied as
 * a pre-existing collection.
 *
 * The initializer defines how the initial values are generated but does not determine how those values are *
 * represented, stored, accessed, or managed by the consuming storage.
 * @since 1.0.0
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Initializer<TKey, TValue>
 */
final readonly class ArrayCallbackInit implements Initializer {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param Closure():array<TKey, TValue> $callback <p>
     * The callback to invoke for each requested value.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Closure $callback
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return array<TKey, TValue> The array of values to use as the initial data for the storage.
     */
    public function initialize ():array {

        return ($this->callback)();

    }

}