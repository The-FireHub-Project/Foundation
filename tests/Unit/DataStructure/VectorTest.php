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
use FireHub\Foundation\DataStructure\Vector;
use FireHub\Foundation\DataStructure\Storage\ListStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Vector data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Vector::class)]
#[CoversClass(ListStorage::class)]
final class VectorTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testToArray (ListStorage $storage):void {

        self::assertSame([1, 2, 3], new Vector($storage)->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testIsEmpty (ListStorage $storage):void {

        self::assertFalse(new Vector($storage)->isEmpty());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testSize (ListStorage $storage):void {

        self::assertSame(3, new Vector($storage)->size());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testFirst (ListStorage $storage):void {

        self::assertSame(1, new Vector($storage)->first()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'emptyList')]
    public function testFirstEmpty (ListStorage $storage):void {

        $this->expectException(NoValueException::class);

        new Vector($storage)->first()->value();

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testLast (ListStorage $storage):void {

        self::assertSame(3, new Vector($storage)->last()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'emptyList')]
    public function testLastEmpty (ListStorage $storage):void {

        $this->expectException(NoValueException::class);

        new Vector($storage)->last()->value();

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testHas (ListStorage $storage):void {

        self::assertTrue(new Vector($storage)->has(0));
        self::assertFalse(new Vector($storage)->has(4));

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testGet (ListStorage $storage):void {

        $vector = new Vector($storage);

        self::assertSame(1, $vector->get(0)->value());

        $this->expectException(NoValueException::class);

        $vector->get(3)->value();

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testInsertFront (ListStorage $storage):void {

        $vector = new Vector($storage);

        $vector->insertFront('x', 'y', 'z');

        self::assertSame(['x', 'y', 'z', 1, 2, 3], $vector->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testInsertBack (ListStorage $storage):void {

        $vector = new Vector($storage);

        $vector->insertBack('x', 'y', 'z');

        self::assertSame([1, 2, 3, 'x', 'y', 'z'], $vector->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testRemoveFront (ListStorage $storage):void {

        $vector = new Vector($storage);

        self::assertSame(1, $vector->removeFront()->value());

        self::assertSame([2, 3], $vector->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testRemoveBack (ListStorage $storage):void {

        $vector = new Vector($storage);

        self::assertSame(3, $vector->removeBack()->value());

        self::assertSame([1, 2], $vector->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testSet (ListStorage $storage):void {

        $vector = new Vector($storage);

        self::assertSame(MutationOutcome::UPDATED, $vector->set(0, 'x'));

        self::assertSame(['x', 2, 3], $vector->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testRemove (ListStorage $storage):void {

        $vector = new Vector($storage);

        self::assertSame(MutationOutcome::REMOVED, $vector->remove(0));

        self::assertSame([2, 3], $vector->toArray());

    }

}