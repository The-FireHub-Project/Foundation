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

use FireHub\Foundation\DataStructure\Map;
use FireHub\Foundation\DataStructure\Storage\HashStorage;
use FireHub\Foundation\DataStructure\Storage\Hash\Engine\BucketHash;
use FireHub\Foundation\DataStructure\Storage\Hash\Strategy\MixedHashStrategy;
use FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit;

/**
 * ### Provides flipping behavior
 *
 * Provides reusable behavior for exchanging the keys and values of an iterable data structure.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 */
trait CanFlip {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::iterate() To iterate over the storage.
     * @uses \FireHub\Foundation\DataStructure\Map::set() To associate a value with a key.
     */
    public function flip ():Map {

        $map = new Map(
            new HashStorage(new BucketHash(new EmptyInit, new MixedHashStrategy))
        );

        foreach ($this->storage->iterate() as $key => $value)
            $map->set($value, $key);

        /** @var \FireHub\Foundation\DataStructure\Map<TValue, TKey> */
        return $map;

    }

}