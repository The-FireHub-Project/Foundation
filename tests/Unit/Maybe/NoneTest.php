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

namespace FireHub\Tests\Foundation\Unit\Maybe;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Maybe\None;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test absence of a value
 * @since 1.0.0
 */
#[Small]
#[Group('maybe')]
#[CoversClass(None::class)]
final class NoneTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     *
     * @return void
     */
    #[TestWith([false])]
    public function testIsSome (bool $expected):void {

        self::assertSame($expected, new None()->isSome());

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     *
     * @return void
     */
    #[TestWith([true])]
    public function testIsNone (bool $expected):void {

        self::assertSame($expected, new None()->isNone());

    }

}