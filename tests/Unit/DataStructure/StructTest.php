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
use FireHub\Foundation\DataStructure\Struct;
use FireHub\Foundation\DataStructure\Storage\HashStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Struct data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Struct::class)]
#[CoversClass(HashStorage::class)]
final class StructTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testToArray (HashStorage $storage):void {

        self::assertSame(['x' => 1, 'y' => 2, 'z' => 3], new Struct($storage)->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testCopy (HashStorage $storage):void {

        $vector = new Struct($storage);

        $copy = $vector->copy();

        self::assertNotSame($storage, $copy);

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testIsEmpty (HashStorage $storage):void {

        self::assertFalse(new Struct($storage)->isEmpty());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testSize (HashStorage $storage):void {

        self::assertSame(3, new Struct($storage)->size());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testHas (HashStorage $storage):void {

        self::assertTrue(new Struct($storage)->has('x'));
        self::assertFalse(new Struct($storage)->has('q'));

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testGet (HashStorage $storage):void {

        $vector = new Struct($storage);

        self::assertSame(1, $vector->get('x')->value());

        $this->expectException(NoValueException::class);

        $vector->get('q')->value();

    }

}