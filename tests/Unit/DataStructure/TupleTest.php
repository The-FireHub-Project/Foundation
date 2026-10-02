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

namespace FireHub\Tests\Foundation\Unit\DataStructure;

use FireHub\Testing\FireHubTestCase;
use FireHub\Core\Type\Exception\NoValueException;
use FireHub\Foundation\DataStructure\Tuple;
use FireHub\Foundation\DataStructure\Storage\FixedStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Tuple data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Tuple::class)]
#[CoversClass(FixedStorage::class)]
final class TupleTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\FixedStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'fixed')]
    public function testToArray (FixedStorage $storage):void {

        self::assertSame(['one', 'two', 'three'], new Tuple($storage)->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\FixedStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'fixed')]
    public function testCopy (FixedStorage $storage):void {

        $vector = new Tuple($storage);

        $copy = $vector->copy();

        self::assertNotSame($storage, $copy);

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\FixedStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'fixed')]
    public function testIsEmpty (FixedStorage $storage):void {

        self::assertFalse(new Tuple($storage)->isEmpty());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\FixedStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'fixed')]
    public function testSize (FixedStorage $storage):void {

        self::assertSame(3, new Tuple($storage)->size());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\FixedStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'fixed')]
    public function testHas (FixedStorage $storage):void {

        self::assertTrue(new Tuple($storage)->has(0));
        self::assertFalse(new Tuple($storage)->has(4));

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\FixedStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'fixed')]
    public function testGet (FixedStorage $storage):void {

        $vector = new Tuple($storage);

        self::assertSame('one', $vector->get(0)->value());

        $this->expectException(NoValueException::class);

        $vector->get(3)->value();

    }

}