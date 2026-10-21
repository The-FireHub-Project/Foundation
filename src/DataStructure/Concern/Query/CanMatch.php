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

namespace FireHub\Foundation\DataStructure\Concern\Query;

use FireHub\Foundation\DataStructure\Query\Matches;

/**
 * ### Provides matching query support
 *
 * Provides reusable access to matching operations for determining whether values, key-value pairs, or elements
 * matching specific conditions exist within a data structure.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
trait CanMatch {

    /**
     * ### Creates a matching query
     *
     * Creates a query for performing non-mutating membership and predicate-based checks against this data
     * structure.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Query\Matches<TKey, TValue> Matching query.
     */
    public function matches ():Matches {

        /** @var \FireHub\Foundation\DataStructure\Query\Matches<TKey, TValue> */
        return new Matches($this);

    }

}