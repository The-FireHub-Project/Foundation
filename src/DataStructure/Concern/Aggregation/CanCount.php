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

namespace FireHub\Foundation\DataStructure\Concern\Aggregation;

use FireHub\Foundation\DataStructure\Aggregation\Count;

/**
 * ### Provides count aggregation capabilities
 *
 * Provides access to higher-level count aggregation operations for values contained within the canonical iteration
 * sequence of a data structure.
 *
 * This trait exposes count aggregation without defining or altering the underlying data structure, storage, or
 * iteration semantics.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
trait CanCount {

    /**
     * ### Creates a count aggregation instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Aggregation\Count<TKey, TValue> The count aggregation.
     */
    public function count ():Count {

        /** @var \FireHub\Foundation\DataStructure\Aggregation\Count<TKey, TValue> */
        return new Count($this);

    }

}