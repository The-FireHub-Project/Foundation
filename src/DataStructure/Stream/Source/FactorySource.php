<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Stream\Source;

use FireHub\Foundation\DataStructure\Stream\Source;
use Closure;

/**
 * ### Provides a Stream Source backed by an iterable factory
 *
 * FactorySource creates the iterable used to produce Stream elements when iteration begins.
 *
 * Unlike a Source backed by the already-created iterable, the factory is invoked for every traversal. This makes it
 * possible to create a fresh iterator or generator for each Stream consumption and therefore enables replayable
 * lazy sequences when the supplied factory itself supports repeated invocation.
 *
 * The iterable returned by the factory is consumed lazily and is not materialized by the Source. The factory may
 * produce finite or unbounded sequences and may get its elements from computation, external resources, or other
 * deferred data providers.
 *
 * FactorySource does not cache produced elements. Each traversal invokes the factory independently.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue>
 */
final readonly class FactorySource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param Closure():iterable<TKey, TValue> $factory <p>
     * The factory that creates the iterable used to produce source elements.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Closure $factory
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function iterate ():iterable {

        yield from ($this->factory)();

    }

}