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

namespace FireHub\Foundation\DataStructure;

use FireHub\Core\Boundary\Type\DataStructure\Stream as StreamBoundary;
use FireHub\Core\Boundary\Capability\Transformation\ {
    Concatenable, Mappable, Rejectable
};
use FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible;
use FireHub\Foundation\DataStructure\Boundary\Transformation\ {
    Skippable, Takeable
};
use FireHub\Foundation\DataStructure\Stream\Source;
use FireHub\Foundation\DataStructure\Stream\Source\ {
    ConcatSource, FilterSource, FlatMapSource, IterableSource, MapSource, SkipSource, TakeSource, TapSource
};
use FireHub\Foundation\DataStructure\Transformation\ {
    Select, Skip, Take
};
use FireHub\Foundation\DataStructure\Concern\Transformation\CanReject;
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
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Mappable<TKey, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Rejectable<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Takeable<TKey, TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Transformation\Skippable<TKey, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Transformation\Concatenable<TValue>
 * @implements \FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible<TValue>
 */
readonly class Stream implements StreamBoundary, Mappable, Rejectable, Takeable, Skippable, Concatenable, Reducible {

    /**
     * ### Provides rejection capabilities
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\DataStructure\Concern\Transformation\CanReject<TKey, TValue>
     */
    use CanReject;

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
    final public function __construct (
        protected Source $source
    ) {}

    /**
     * ### Observes Stream elements
     *
     * Creates a new Stream that invokes the specified callback for each element as it is consumed while preserving the
     * original keys and values.
     *
     * The callback is executed lazily and may be used to perform side effects without modifying the Stream elements.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey=):void $callback <p>
     * The callback invoked for each consumed element.
     * </p>
     *
     * @return static Stream with element observation.
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\TapSource To create a new Stream instance that observes
     * elements.
     */
    public function tap (callable $callback):static {

        return new static(
            new TapSource(
                $this->source,
                $callback(...)
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\MapSource To create a new Stream instance with the
     * mapped elements.
     */
    public function map (callable $callback):static {

        return new static(
            new MapSource($this->source, $callback(...))
        );

    }

    /**
     * ### Flat maps the Stream
     *
     * Creates a new Stream by mapping each element to the iterable and flattening the produced iterables into a single
     * lazy sequence.
     * @since 1.0.0
     *
     * @template TNewKey
     * @template TNewValue
     *
     * @param callable(TValue, TKey=):iterable<TNewKey, TNewValue> $callback <p>
     * The mapping function used to produce values for each Stream element.
     * </p>
     *
     * @return static<TNewKey, TNewValue> The flat mapped Stream.
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\FlatMapSource To create a new Stream instance with the
     * flat mapped elements,
     */
    public function flatMap (callable $callback):static {

        return new static(
            new FlatMapSource(
                $this->source,
                $callback(...)
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\FilterSource To create a new Stream instance with the
     * filtered elements.
     */
    public function filter (callable $callback):static {

        return new static(
            new FilterSource($this->source, $callback(...))
        );

    }

    /**
     * ### Creates a Select instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Select<int, TValue, $this> A select transformation of
     * the data structure.
     */
    public function select ():Select {

        /** @var Select<int, TValue, $this> */
        return new Select($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\TakeSource To create a new Stream instance with the
     * limited number of elements.
     */
    public function takeWhile (callable $callback):static {

        return new static(
            new TakeSource(
                $this->source,
                $callback(...)
            )
        );

    }

    /**
     * ### Creates a Take instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Take<TKey, TValue, $this> A take transformation of the
     * data structure.
     */
    public function take ():Take {

        /** @var Take<TKey, TValue, $this> */
        return new Take($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\SkipSource To create a new Stream instance with the
     * skipped elements.
     */
    public function skipWhile (callable $callback):static {

        return new static(
            new SkipSource(
                $this->source,
                $callback(...)
            )
        );

    }

    /**
     * ### Creates a Skip instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Skip<TKey, TValue, $this> A skip transformation of the
     * data structure.
     */
    public function skip ():Skip {

        /** @var Skip<TKey, TValue, $this> */
        return new Skip($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source\ConcatSource To create a new Stream instance with
     * the concatenated elements.
     *
     * @param iterable<TKey, TValue> $values <p>
     * The values to concatenate.
     * </p>
     */
    public function concat (iterable $values):static {

        return clone($this, [
            'source' => new ConcatSource(
                $this->source,
                new IterableSource($values)
            )
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source::iterate() To iterate over the Source elements.
     */
    public function reduce (mixed $initial, callable $callback):mixed {

        $carry = $initial;

        foreach ($this->source->iterate() as $value)
            $carry = $callback($carry, $value);

        return $carry;

    }

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