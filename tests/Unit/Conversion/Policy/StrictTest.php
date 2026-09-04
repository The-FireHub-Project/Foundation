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

namespace FireHub\Tests\Foundation\Unit\Policy;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Conversion\Policy\Strict;
use FireHub\Foundation\Conversion\Exception\ConversionException;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Performs strict value conversions
 * @since 1.0.0
 */
#[Small]
#[Group('conversion')]
#[CoversClass(Strict::class)]
final class StrictTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param null|array<array-key, mixed> $expected
     * @param mixed $value
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException
     *
     * @return void
     */
    #[TestWith([[1, 2, 3], [1, 2, 3]])]
    public function testArray (?array $expected, mixed $value):void {

        self::assertSame($expected, new Strict($value)->array());

    }

    /**
     * @since 1.0.0
     *
     * @param null|array<array-key, mixed> $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([null, null])]
    public function testArrayException (?array $expected, mixed $value):void {

        $this->expectException(ConversionException::class);

        self::assertSame($expected, new Strict($value)->array());

    }

    /**
     * @since 1.0.0
     *
     * @param null|bool $expected
     * @param mixed $value
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException
     *
     * @return void
     */
    #[TestWith([true, 1])]
    #[TestWith([false, 0])]
    #[TestWith([true, '1'])]
    #[TestWith([false, '0'])]
    #[TestWith([true, 'yes'])]
    #[TestWith([false, 'no'])]
    #[TestWith([true, 'on'])]
    #[TestWith([false, 'off'])]
    public function testBool (?bool $expected, mixed $value):void {

        self::assertSame($expected, new Strict($value)->bool());

    }

    /**
     * @since 1.0.0
     *
     * @param null|bool $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([null, null])]
    public function testBoolException (?bool $expected, mixed $value):void {

        $this->expectException(ConversionException::class);

        self::assertSame($expected, new Strict($value)->bool());

    }

    /**
     * @since 1.0.0
     *
     * @param null|float $expected
     * @param mixed $value
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException
     *
     * @return void
     */
    #[TestWith([10, '10'])]
    public function testFloat (?float $expected, mixed $value):void {

        self::assertSame($expected, new Strict($value)->float());

    }

    /**
     * @since 1.0.0
     *
     * @param null|float $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([null, null])]
    public function testFloatException (?float $expected, mixed $value):void {

        $this->expectException(ConversionException::class);

        self::assertSame($expected, new Strict($value)->float());

    }

    /**
     * @since 1.0.0
     *
     * @param null|int $expected
     * @param mixed $value
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([10, '10'])]
    public function testInt (?int $expected, mixed $value):void {

        self::assertSame($expected, new Strict($value)->int());

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
    #[TestWith([null, '10.12'])]
    public function testIntException (?int $expected, mixed $value):void {

        $this->expectException(ConversionException::class);

        self::assertSame($expected, new Strict($value)->int());

    }

    /**
     * @since 1.0.0
     *
     * @param null|string $expected
     * @param mixed $value
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException
     *
     * @return void
     */
    #[TestWith(['20', 20])]
    public function testString (?string $expected, mixed $value):void {

        self::assertSame($expected, new Strict($value)->string());

    }

    /**
     * @since 1.0.0
     *
     * @param null|string $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([null, null])]
    public function testStringException (?string $expected, mixed $value):void {

        $this->expectException(ConversionException::class);

        self::assertSame($expected, new Strict($value)->string());

    }

}