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
use FireHub\Foundation\DataStructure\Bag;
use FireHub\Foundation\DataStructure\Storage\HashBagStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Bag data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Bag::class)]
#[CoversClass(HashBagStorage::class)]
final class BagTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testToArray (HashBagStorage $storage):void {

        self::assertSame(
            ['John', 'John', 'John', 'Jane', 'Jane', 'Richard'],
            new Bag($storage)->toArray()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testCopy (HashBagStorage $storage):void {

        $bag = new Bag($storage);

        $copy = $bag->copy();
        $copy->add('Jana');

        self::assertNotSame($storage, $copy);

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testFork (HashBagStorage $storage):void {

        $bag = new Bag($storage);

        $fork = $bag->fork();
        $fork->add('Jana');

        self::assertNotSame($storage, $fork);

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testIsEmpty (HashBagStorage $storage):void {

        self::assertFalse(new Bag($storage)->isEmpty());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testSize (HashBagStorage $storage):void {

        self::assertSame(6, new Bag($storage)->size());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testDistinctSize (HashBagStorage $storage):void {

        self::assertSame(3, new Bag($storage)->distinctSize());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testContains (HashBagStorage $storage):void {

        self::assertTrue(new Bag($storage)->contains('John'));
        self::assertFalse(new Bag($storage)->contains('Jana'));

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testFrequency (HashBagStorage $storage):void {

        self::assertSame(3, new Bag($storage)->frequency('John'));
        self::assertSame(0, new Bag($storage)->frequency('Jana'));

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testSet (HashBagStorage $storage):void {

        $bag = new Bag($storage);

        self::assertSame(MutationOutcome::UPDATED, $bag->add('John'));

        self::assertSame(MutationOutcome::CREATED, $bag->add('Jana'));

        self::assertSame(
            ['John', 'John', 'John', 'John', 'Jane', 'Jane', 'Richard', 'Jana'],
            new Bag($storage)->toArray()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testRemove (HashBagStorage $storage):void {

        $bag = new Bag($storage);

        self::assertSame(MutationOutcome::REMOVED, $bag->remove('Richard'));

        self::assertSame(MutationOutcome::UPDATED, $bag->remove('John'));

        self::assertSame(MutationOutcome::NOT_FOUND, $bag->remove('Jana'));

        self::assertSame(
            ['John', 'John', 'Jane', 'Jane'],
            new Bag($storage)->toArray()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\HashBagStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'hashBag')]
    public function testRemoveAll (HashBagStorage $storage):void {

        $bag = new Bag($storage);

        self::assertSame(MutationOutcome::REMOVED, $bag->removeAll('John'));

        self::assertSame(MutationOutcome::NOT_FOUND, $bag->removeAll('Jana'));

        self::assertSame(
            ['Jane', 'Jane', 'Richard'],
            new Bag($storage)->toArray()
        );

    }

}