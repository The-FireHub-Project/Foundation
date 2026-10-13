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
use FireHub\Foundation\Maybe\Some;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test an existing value
 * @since 1.0.0
 */
#[Small]
#[Group('maybe')]
#[CoversClass(Some::class)]
final class SomeTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([true, 'test'])]
    public function testIsSome (bool $expected, mixed $value):void {

        self::assertSame($expected, new Some($value)->isSome());

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([false, 'test'])]
    public function testIsNone (bool $expected, mixed $value):void {

        self::assertSame($expected, new Some($value)->isNone());

    }

    /**
     * @since 1.0.0
     *
     * @param mixed $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith(['test', 'test'])]
    public function testValue (mixed $expected, mixed $value):void {

        self::assertSame($expected, new Some($value)->value());

    }

}