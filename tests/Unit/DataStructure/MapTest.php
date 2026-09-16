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
use FireHub\Core\Meta\Enum\MutationOutcome;
use FireHub\Core\Type\Exception\NoValueException;
use FireHub\Foundation\DataStructure\Map;
use FireHub\Foundation\DataStructure\Storage\HashStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Map data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Map::class)]
#[CoversClass(HashStorage::class)]
final class MapTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testToArray (HashStorage $storage):void {

        self::assertSame([
            ['key' => 'x', 'value' => 1],
            ['key' => 'y', 'value' => 2],
            ['key' => 'z', 'value' => 3]
        ], new Map($storage)->toArray());

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

        $map = new Map($storage);

        $copy = $map->copy();
        $copy->set('x', 5);

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
    public function testFork (HashStorage $storage):void {

        $map = new Map($storage);

        $copy = $map->fork();
        $copy->set('x', 5);

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

        self::assertFalse(new Map($storage)->isEmpty());

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

        self::assertSame(3, new Map($storage)->size());

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

        self::assertTrue(new Map($storage)->has('x'));
        self::assertFalse(new Map($storage)->has('q'));

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

        $map = new Map($storage);

        self::assertSame(1, $map->get('x')->value());

        $this->expectException(NoValueException::class);

        $map->get('q')->value();

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testSet (HashStorage $storage):void {

        $vector = new Map($storage);

        self::assertSame(MutationOutcome::UPDATED, $vector->set('x', 11));

        self::assertSame([
            ['key' => 'x', 'value' => 11],
            ['key' => 'y', 'value' => 2],
            ['key' => 'z', 'value' => 3]
        ], $vector->toArray());

        self::assertSame(MutationOutcome::CREATED, $vector->set('q', 4));

        self::assertSame([
            ['key' => 'x', 'value' => 11],
            ['key' => 'y', 'value' => 2],
            ['key' => 'z', 'value' => 3],
            ['key' => 'q', 'value' => 4]
        ], $vector->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hash')]
    public function testRemove (HashStorage $storage):void {

        $vector = new Map($storage);

        self::assertSame(MutationOutcome::REMOVED, $vector->remove('x'));

        self::assertSame([
            ['key' => 'y', 'value' => 2],
            ['key' => 'z', 'value' => 3]
        ], $vector->toArray());

    }

}