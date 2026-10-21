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

use FireHub\Foundation\DataStructure\Query\Find;

/**
 * ### Provides finding query support
 *
 * Provides reusable access to finding operations for locating values, keys, or elements matching specific
 * conditions within a data structure.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
trait CanFind {

    /**
     * ### Creates a finding query
     *
     * Creates a query for performing non-mutating lookup and positional discovery operations against this data
     * structure.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Query\Find<TKey, TValue> Finding query.
     */
    public function find ():Find {

        /** @var \FireHub\Foundation\DataStructure\Query\Find<TKey, TValue> */
        return new Find($this);

    }

}