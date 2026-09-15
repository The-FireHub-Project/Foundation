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
use FireHub\Foundation\DataStructure\Stack;
use FireHub\Foundation\DataStructure\Storage\ListStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Stack data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Stack::class)]
#[CoversClass(ListStorage::class)]
final class StackTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testToArray (ListStorage $storage):void {

        self::assertSame([1, 2, 3], new Stack($storage)->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testCopy (ListStorage $storage):void {

        $vector = new Stack($storage);

        $copy = $vector->copy();
        $copy->insertBack('x');

        self::assertNotSame($storage, $copy);

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

        self::assertFalse(new Stack($storage)->isEmpty());

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

        self::assertSame(3, new Stack($storage)->size());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testPeek (ListStorage $storage):void {

        self::assertSame(3, new Stack($storage)->peek()->value());

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

        new Stack($storage)->last()->value();

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testAppend (ListStorage $storage):void {

        $vector = new Stack($storage);

        $vector->append('x', 'y', 'z');

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
    public function testPop (ListStorage $storage):void {

        $vector = new Stack($storage);

        self::assertSame(3, $vector->pop()->value());

        self::assertSame([1, 2], $vector->toArray());

    }

}