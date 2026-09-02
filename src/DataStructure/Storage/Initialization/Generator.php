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
use Generator as InternalGenerator, Closure;

/**
 * ### Provides an initialization strategy based on a generator
 *
 * Generator initializer supplies the initial contents of a storage from a generator, allowing values to be produced
 * lazily as they are requested. This avoids requiring all initial values to exist in memory before the storage is
 * initialized.
 *
 * The initializer defines how the initial values are generated but does not determine how those values are
 * represented, stored, accessed, or managed by the consuming storage.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Initializer<TKey, TValue>
 */
final readonly class Generator implements Initializer {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param Closure():InternalGenerator<TKey, TValue> $callback <p>
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
     * @return InternalGenerator<TKey, TValue> The generator yielding the initial contents of the storage.
     */
    public function initialize ():InternalGenerator {

        return ($this->callback)();

    }

}