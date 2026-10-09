<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Stream\Source;

use FireHub\Foundation\DataStructure\Stream\Source;
use FireHub\Foundation\DataStructure\Exception\ChunkSizeException;

/**
 * ### Provides a Stream Source that lazily chunks values
 *
 * ChunkSource decorates another Source and groups consecutive values into chunks containing at most the configured
 * number of elements.
 *
 * Values are consumed lazily from the underlying Source and buffered only until the current chunk reaches the
 * configured size. The final chunk may contain fewer elements when the underlying Source is exhausted before the
 * configured chunk size is reached.
 *
 * Original Source keys are not preserved. Values within each chunk are indexed sequentially starting from zero,
 * while produced chunks are also indexed sequentially starting from zero.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Stream\Source<int, list<TValue>>
 */
final readonly class ChunkSource implements Source {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Stream\Source<TKey, TValue> $source <p>
     * The Source whose values are chunked.
     * </p>
     * @param positive-int $size <p>
     * The maximum number of values in each chunk.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\ChunkSizeException  If the specified chunk size is less
     * than one.
     *
     * @return void
     */
    public function __construct (
        private Source $source,
        private int $size
    ) {

        if ($this->size < 1)
            throw new ChunkSizeException;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Stream\Source::iterate() To iterate over the underlying Source elements.
     */
    public function iterate ():iterable {

        $chunk = []; $count = 0; $index = 0;
        foreach ($this->source->iterate() as $value) {

            $chunk[] = $value;

            if (++$count < $this->size)
                continue;

            yield $index++ => $chunk;

            $chunk = [];
            $count = 0;

        }

        if ($count > 0)
            yield $index => $chunk;

    }

}