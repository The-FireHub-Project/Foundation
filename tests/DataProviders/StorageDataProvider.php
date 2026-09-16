<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation\Tests
 */

namespace FireHub\Tests\Foundation\DataProviders;

use FireHub\Foundation\DataStructure\Storage\ {
    HashStorage, ListStorage
};
use FireHub\Foundation\DataStructure\Storage\Hash\Engine\ArrHash;
use FireHub\Foundation\DataStructure\Storage\Initialization\ {
    ArrayInit, EmptyInit
};

/**
 * ### Storage data provider
 * @since 1.0.0
 */
final class StorageDataProvider {

    /**
     * @since 1.0.0
     *
     * @return array<\FireHub\Foundation\DataStructure\Storage\ListStorage<mixed>>
     */
    public static function emptyList ():array {

        return [
            [new ListStorage(new EmptyInit())]
        ];

    }

    /**
     * @since 1.0.0
     *
     * @return array<\FireHub\Foundation\DataStructure\Storage\ListStorage<mixed>>
     */
    public static function list ():array {

        return [
            [new ListStorage(new ArrayInit([1, 2, 3]))]
        ];

    }

    /**
     * @since 1.0.0
     *
     * @return array<\FireHub\Foundation\DataStructure\Storage\HashStorage<array-key, mixed>>
     */
    public static function emptyHash ():array {

        return [
            [new HashStorage(new ArrHash(new ArrayInit([])))]
        ];

    }

    /**
     * @since 1.0.0
     *
     * @return array<\FireHub\Foundation\DataStructure\Storage\HashStorage<array-key, mixed>>
     */
    public static function hash ():array {

        return [
            [new HashStorage(new ArrHash(new ArrayInit(['x' => 1, 'y' => 2, 'z' => 3])))]
        ];

    }

}