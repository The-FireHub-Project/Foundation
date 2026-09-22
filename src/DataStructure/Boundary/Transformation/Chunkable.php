<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Boundary\Transformation;

use FireHub\Foundation\DataStructure\Stream;

/**
 * ### Chunkable data structure
 *
 * Defines a data structure that can partition its elements into consecutive chunks according to dynamically
 * determined boundaries.
 *
 * Each generated chunk preserves the concrete type and semantics of the source data structure, while the resulting
 * chunks are exposed as a lazy stream.
 *
 * A chunk boundary is determined by evaluating a callback for each source element. When the callback evaluates to
 * true, the current element starts a new chunk. The first source element always starts the first chunk and therefore
 * does not require an explicit boundary.
 *
 * This boundary defines the primitive chunking operation used by higher-level Foundation transformations such as
 * fixed-size, conditional, and other derived chunking strategies.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
interface Chunkable {

    /**
     * ### Chunks elements by dynamically determined boundaries
     *
     * Lazily partitions the source elements into consecutive chunks according to boundaries determined by the
     * supplied callback.
     *
     * The callback is evaluated for each source element. When it evaluates to true, the current element starts a new
     * chunk. The first source element always starts the first chunk regardless of the callback result.
     *
     * Each generated chunk preserves the concrete type and semantics of the source data structure.
     * @since 1.0.0
     *
     * @param callable(TValue, TKey):bool $callback <p>
     * Callback that determines whether the current element starts a new chunk.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, static> A lazy stream containing the generated chunks.
     */
    public function chunkBy (callable $callback):Stream;

}