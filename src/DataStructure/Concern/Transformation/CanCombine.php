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

use FireHub\Foundation\DataStructure\ {
    DS, Map
};
use FireHub\Foundation\DataStructure\Exception\CombinationSizeMismatch;

/**
 * ### Provides combining capabilities
 *
 * Provides reusable functionality for combining values from the current data structure with values from another
 * iterable to create a map.
 *
 * Values from the current data structure become keys of the resulting map, while values from the provided iterable
 * become their corresponding values.
 * @since 1.0.0
 *
 * @template TKey
 */
trait CanCombine {

    /**
     * ### Combines values into a map
     *
     * Combines values from the current data structure with values from the provided iterable, using values from the
     * current data structure as keys of the resulting map.
     *
     * Both sources must contain the same number of elements.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Map::set() To set the key-value pair in the map.
     *
     * @template TCombineValue
     *
     * @param iterable<TCombineValue> $values <p>
     * Values to associate with the values of the current data structure.
     * </p>
     *
     * @throws \FireHub\Foundation\DataStructure\Exception\CombinationSizeMismatch If the number of keys and values
     * does not match.
     *
     * @return \FireHub\Foundation\DataStructure\Map<TKey, TCombineValue> Map containing the combined keys and values.
     */
    public function combine (iterable $values):Map {

        $map = DS::map()->bucket()->empty();

        $iterator = (static fn() => yield from $values)();

        $iterator->rewind();

        foreach ($this as $key) {

            if (!$iterator->valid())
                throw new CombinationSizeMismatch(
                    'Cannot combine data structures with a different number of elements.'
                );

            $map->set($key, $iterator->current());

            $iterator->next();

        }

        if ($iterator->valid())
            throw new CombinationSizeMismatch(
                'Cannot combine data structures with a different number of elements.'
            );

        /** @var \FireHub\Foundation\DataStructure\Map<TKey, TCombineValue> */
        return $map;

    }

}