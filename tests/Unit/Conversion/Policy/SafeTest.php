<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Foundation\Tests
 */

namespace FireHub\Tests\Foundation\Unit\Policy;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Conversion\Policy\Safe;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Performs safe value conversions
 * @since 1.0.0
 */
#[Small]
#[Group('conversion')]
#[CoversClass(Safe::class)]
final class SafeTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param null|array<array-key, mixed> $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([[1, 2, 3], [1, 2, 3]])]
    #[TestWith([null, null])]
    public function testArray (?array $expected, mixed $value):void {

        self::assertSame($expected, new Safe($value)->array());

    }

    /**
     * @since 1.0.0
     *
     * @param null|bool $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([false, 'off'])]
    #[TestWith([null, null])]
    public function testBool (?bool $expected, mixed $value):void {

        self::assertSame($expected, new Safe($value)->bool());

    }

    /**
     * @since 1.0.0
     *
     * @param null|float $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([10, '10'])]
    #[TestWith([null, null])]
    public function testFloat (?float $expected, mixed $value):void {

        self::assertSame($expected, new Safe($value)->float());

    }

    /**
     * @since 1.0.0
     *
     * @param null|int $expected
     * @param mixed $value
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([10, '10'])]
    #[TestWith([null, '10.12'])]
    public function testInt (?int $expected, mixed $value):void {

        self::assertSame($expected, new Safe($value)->int());

    }

    /**
     * @since 1.0.0
     *
     * @param null|string $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith(['20', 20])]
    #[TestWith([null, null])]
    public function testString (?string $expected, mixed $value):void {

        self::assertSame($expected, new Safe($value)->string());

    }

}