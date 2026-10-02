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

use FireHub\Foundation\DataStructure\ {
    DS, Vector
};

/**
 * ### Provides value extraction capabilities
 *
 * Provides reusable functionality for extracting the values of an enumerable data structure into a vector while
 * preserving their iteration order.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
trait CanExtractValues {

    /**
     * ### Extracts the values
     *
     * Creates a vector containing all values from the data structure in their iteration order.
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Vector<TValue> Vector containing the extracted values.
     */
    public function values ():Vector {

        $values = DS::vector()->empty();

        foreach ($this as $value)
            $values->append($value);

        /** @var \FireHub\Foundation\DataStructure\Vector<TValue> */
        return $values;

    }

}