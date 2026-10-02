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

namespace FireHub\Foundation\DataStructure\Concern\Projection;

use FireHub\Core\Type\DataStructure\Map\Entry;
use FireHub\Foundation\DataStructure\ {
    DS, Vector
};

/**
 * ### Provides entry extraction capabilities
 *
 * Provides reusable functionality for extracting the entries of an enumerable data structure into a vector while
 * preserving their iteration order.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
trait CanExtractEntries {

    /**
     * ### Extracts the entries
     *
     * Creates a vector containing all entries from the data structure in their iteration order.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Vector<Entry<TKey, TValue>> Vector containing the extracted entries.
     */
    public function entries ():Vector {

        $entries = DS::vector()->empty();

        foreach ($this as $key => $value)
            $entries->append(new Entry($key, $value));

        /** @var \FireHub\Foundation\DataStructure\Vector<Entry<TKey, TValue>> */
        return $entries;

    }

}