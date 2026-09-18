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
use FireHub\Foundation\DataStructure\Deque;
use FireHub\Foundation\DataStructure\Storage\ListStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Deque data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Deque::class)]
#[CoversClass(ListStorage::class)]
final class DequeTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testToArray (ListStorage $storage):void {

        self::assertSame([1, 2, 3], new Deque($storage)->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testCopy (ListStorage $storage):void {

        $deque = new Deque($storage);

        $copy = $deque->copy();
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

        self::assertFalse(new Deque($storage)->isEmpty());

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

        self::assertSame(3, new Deque($storage)->size());

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

        self::assertSame(1, new Deque($storage)->first()->value());

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

        new Deque($storage)->first()->value();

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

        self::assertSame(3, new Deque($storage)->last()->value());

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

        new Deque($storage)->last()->value();

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testPrepend (ListStorage $storage):void {

        $deque = new Deque($storage);

        $deque->prepend('x', 'y', 'z');

        self::assertSame(['x', 'y', 'z', 1, 2, 3], $deque->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testAppend (ListStorage $storage):void {

        $deque = new Deque($storage);

        $deque->append('x', 'y', 'z');

        self::assertSame([1, 2, 3, 'x', 'y', 'z'], $deque->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testShift (ListStorage $storage):void {

        $deque = new Deque($storage);

        self::assertSame(1, $deque->shift()->value());

        self::assertSame([2, 3], $deque->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testPop (ListStorage $storage):void {

        $deque = new Deque($storage);

        self::assertSame(3, $deque->pop()->value());

        self::assertSame([1, 2], $deque->toArray());

    }

}