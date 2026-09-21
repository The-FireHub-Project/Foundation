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

namespace FireHub\Foundation\DataStructure\Concern\Transformation;

/**
 * ### Provides rejection through filtering
 *
 * Provides a reusable implementation of element rejection for data structures that support filtering.
 *
 * Rejection is performed by inverting the supplied predicate and delegating the resulting operation to the
 * data structure's filter implementation. Elements for which the predicate evaluates to true are therefore excluded
 * from the resulting data structure.
 *
 * This trait does not define how filtering is performed. Concrete implementations retain their own filtering
 * behavior, including storage-specific optimizations and lazy evaluation semantics.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @mixin \FireHub\Core\Boundary\Capability\Transformation\Filterable<TKey, TValue>
 */
trait CanReject {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses static::filter() To filter the values using the provided callback.
     */
    public function reject (callable $callback):static {

        return $this->filter(
            fn ($value, $key = null):bool => !$callback($value, $key)
        );

    }

}