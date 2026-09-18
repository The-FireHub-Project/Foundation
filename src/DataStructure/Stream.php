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

namespace FireHub\Foundation\DataStructure;

use FireHub\Core\Boundary\Type\DataStructure\Stream as StreamBoundary;
use FireHub\Foundation\DataStructure\Stream\Source;
use Traversable;

/**
 * ### Stream data structure
 *
 * Represents a sequence of key-value pairs that may be produced lazily as they are consumed.
 *
 * Stream gets its elements from an underlying Source and does not require the complete sequence to be
 * materialized in memory before iteration begins. This allows finite, unbounded, dynamically generated, and
 * externally produced sequences to be processed through a common data structure abstraction.
 *
 * The Stream delegates element production to its Source while defining the public data structure semantics exposed
 * to consumers. Whether the Stream can be consumed multiple times depends on the replayability characteristics of
 * the underlying Source.
 *
 * Stream processing operations may create new Stream instances backed by decorated Sources, allowing transformations
 * to be composed into a lazy pipeline without executing them until the resulting Stream is consumed.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Core\Boundary\Type\DataStructure\Stream<TKey, TValue>
 */
readonly class Stream implements StreamBoundary {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source used to produce Stream elements.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected Source $source
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source::iterate() To iterate over the Source elements.
     */
    public function getIterator ():Traversable {

        yield from $this->source->iterate();

    }

}