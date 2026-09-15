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
use FireHub\Foundation\DataStructure\Queue;
use FireHub\Foundation\DataStructure\Storage\ListStorage;
use FireHub\Tests\Foundation\DataProviders\StorageDataProvider;
use PHPUnit\Framework\Attributes\ {
    CoversClass, DataProviderExternal, Group, Small
};

/**
 * ### Test Queue data structure
 * @since 1.0.0
 */
#[Small]
#[Group('data-structure')]
#[CoversClass(Queue::class)]
#[CoversClass(ListStorage::class)]
final class QueueTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testToArray (ListStorage $storage):void {

        self::assertSame([1, 2, 3], new Queue($storage)->toArray());

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

        $vector = new Queue($storage);

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

        self::assertFalse(new Queue($storage)->isEmpty());

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

        self::assertSame(3, new Queue($storage)->size());

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

        self::assertSame(1, new Queue($storage)->peek()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'emptyList')]
    public function testPeekEmpty (ListStorage $storage):void {

        $this->expectException(NoValueException::class);

        new Queue($storage)->peek()->value();

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testEnqueue (ListStorage $storage):void {

        $stack = new Queue($storage);

        $stack->enqueue('x', 'y', 'z');

        self::assertSame([1, 2, 3, 'x', 'y', 'z'], $stack->toArray());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\DataStructure\Storage\ListStorage $storage
     *
     * @return void
     */
    #[DataProviderExternal(StorageDataProvider::class, 'list')]
    public function testDequeue (ListStorage $storage):void {

        $stack = new Queue($storage);

        $stack->dequeue();

        self::assertSame([2, 3], $stack->toArray());

    }

}