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
use FireHub\Foundation\DataStructure\Set;
use FireHub\Foundation\DataStructure\Storage\HashSetStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Set data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Set::class)]
#[CoversClass(HashSetStorage::class)]
final class SetTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashSetStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashSet')]
    public function testToArray (HashSetStorage $storage):void {

        self::assertSame(['John', 'Jane', 'Richard'], new Set($storage)->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashSetStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashSet')]
    public function testCopy (HashSetStorage $storage):void {

        $set = new Set($storage);

        $copy = $set->copy();
        $copy->add('Jana');

        self::assertNotSame($storage, $copy);

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashSetStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashSet')]
    public function testFork (HashSetStorage $storage):void {

        $set = new Set($storage);

        $fork = $set->fork();
        $fork->add('Jana');

        self::assertNotSame($storage, $fork);

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashSetStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashSet')]
    public function testIsEmpty (HashSetStorage $storage):void {

        self::assertFalse(new Set($storage)->isEmpty());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashSetStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashSet')]
    public function testSize (HashSetStorage $storage):void {

        self::assertSame(3, new Set($storage)->size());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashSetStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashSet')]
    public function testContains (HashSetStorage $storage):void {

        self::assertTrue(new Set($storage)->contains('John'));
        self::assertFalse(new Set($storage)->contains('Jana'));

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashSetStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashSet')]
    public function testSet (HashSetStorage $storage):void {

        $set = new Set($storage);

        self::assertSame(MutationOutcome::ALREADY_EXISTS, $set->add('John'));

        self::assertSame(MutationOutcome::CREATED, $set->add('Jana'));

        self::assertSame(['John', 'Jane', 'Richard', 'Jana'], $set->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashSetStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashSet')]
    public function testRemove (HashSetStorage $storage):void {

        $set = new Set($storage);

        self::assertSame(MutationOutcome::REMOVED, $set->remove('John'));

        self::assertSame(MutationOutcome::NOT_FOUND, $set->remove('Jana'));

        self::assertSame(['Jane', 'Richard'], $set->toArray());

    }

}