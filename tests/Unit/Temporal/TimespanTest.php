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

namespace FireHub\Tests\Foundation\Unit\Temporal;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Temporal\ {
    Timespan\Components, Timespan
};
use FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable temporal Timespan Value Object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(Timespan::class)]
final class TimespanTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     *
     * @return void
     */
    #[TestWith(['test'])]
    public function testCreateException (string $value):void {

        $this->expectException(InvalidTimespanTicks::class);

        new Timespan($value);

    }

    /**
     * @since 1.0.0
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents
     *
     * @return void
     */

    public function testComponents ():void {

        self::assertInstanceOf(Components::class, new Timespan('1')->components());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith(['100', '100'])]
    public function testValue (string $expected, string $value):void {

        self::assertSame($expected, new Timespan($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $ticks
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['101', '100', '1'])]
    public function testAddMicroseconds (string $expected, string $ticks, string $value):void {

        self::assertSame($expected, new Timespan($ticks)->addMicroseconds($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $ticks
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['1100', '100', '1'])]
    public function testAddMilliseconds (string $expected, string $ticks, string $value):void {

        self::assertSame($expected, new Timespan($ticks)->addMilliseconds($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $ticks
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['1000100', '100', '1'])]
    public function testAddSeconds (string $expected, string $ticks, string $value):void {

        self::assertSame($expected, new Timespan($ticks)->addSeconds($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $ticks
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['60000100', '100', '1'])]
    public function testAddMinutes (string $expected, string $ticks, string $value):void {

        self::assertSame($expected, new Timespan($ticks)->addMinutes($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $ticks
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['3600000100', '100', '1'])]
    public function testAddHours (string $expected, string $ticks, string $value):void {

        self::assertSame($expected, new Timespan($ticks)->addHours($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $ticks
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['86400000100', '100', '1'])]
    public function testAddDays (string $expected, string $ticks, string $value):void {

        self::assertSame($expected, new Timespan($ticks)->addDays($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['86400000000', '86400000000'])]
    public function testMicroseconds (string $expected, string $value):void {

        self::assertSame($expected, new Timespan($value)->microseconds());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['86400000', '86400000000'])]
    public function testMilliseconds (string $expected, string $value):void {

        self::assertSame($expected, new Timespan($value)->milliseconds());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['86400', '86400000000'])]
    public function testSeconds (string $expected, string $value):void {

        self::assertSame($expected, new Timespan($value)->seconds());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['1440', '86400000000'])]
    public function testMinutes (string $expected, string $value):void {

        self::assertSame($expected, new Timespan($value)->minutes());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['24', '86400000000'])]
    public function testHours (string $expected, string $value):void {

        self::assertSame($expected, new Timespan($value)->hours());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException
     *
     * @return void
     */
    #[TestWith(['1', '86400000000'])]
    public function testDays (string $expected, string $value):void {

        self::assertSame($expected, new Timespan($value)->days());

    }

}