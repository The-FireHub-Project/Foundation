<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.0
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Concern\Aggregation;

use FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible;

/**
 * ### Provides value reduction
 *
 * Provides reusable reduction behavior for data structures by reducing their values into a single accumulated
 * result.
 *
 * When the underlying storage provides a specialized reduction implementation, that implementation is used as a
 * fast path. Otherwise, values are reduced using storage iteration.
 * @since 1.0.0
 *
 * @template TValue
 */
trait CanReduce {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Boundary\Aggregation\Reducible::reduce() To use a specialized storage
     * reduction implementation when available.
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     */
    public function reduce (mixed $initial, callable $callback):mixed {

        if ($this->storage instanceof Reducible)
            return $this->storage->reduce($initial, $callback);

        $carry = $initial;

        foreach ($this->storage->iterate() as $value)
            $carry = $callback($carry, $value);

        return $carry;

    }

}