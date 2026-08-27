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
use FireHub\Core\Type\Date\Zone;
use FireHub\Core\Meta\Enum\Date\ {
    Month, WeekDay
};
use FireHub\Core\Meta\Enum\Date\ {
    Format\Token, Format
};
use FireHub\Foundation\Temporal\ {
    DateTime, NamedTimezone, FixedTimezone
};
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable date and time value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(DateTime::class)]
final class DateTimeTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param null|NamedTimezone|FixedTimezone $timezone
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.000000', '2000-01-01 12:00:00'])]
    public function testFrom (string $expected, string $value, null|NamedTimezone|FixedTimezone $timezone = null):void {

        self::assertSame($expected, DateTime::from($value, $timezone)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Format $format
     * @param null|NamedTimezone|FixedTimezone $timezone
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.000000', '01.01.2000 12.00.00', 'd.m.Y H.i.s'])]
    public function testFromFormat (string $expected, string $value, string|Format $format = Format::ISO_DATE_TIME, null|NamedTimezone|FixedTimezone $timezone = null):void {

        self::assertSame($expected, DateTime::fromFormat($value, $format, $timezone)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param null|NamedTimezone|FixedTimezone $timezone
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.000000'])]
    public function testFromTimestamp (string $expected, null|NamedTimezone|FixedTimezone $timezone = null):void {

        self::assertSame($expected, DateTime::fromTimestamp(DateTime::from($expected)->timestamp(), $timezone)->value());

    }

    /**
     * @since 1.0.0
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    public function testTimezone ():void {

        self::assertEquals(
            new NamedTimezone(Zone::UTC),
            DateTime::from('2000-01-01 12:00:00')->timezone()
        );

        self::assertEquals(
            new NamedTimezone(Zone::ARCTIC_LONGYEARBYEN),
            DateTime::from('2000-01-01 12:00:00', new NamedTimezone(Zone::ARCTIC_LONGYEARBYEN))->timezone()
        );

        self::assertEquals(
            new FixedTimezone('+0100'),
            DateTime::from('2000-01-01 12:00:00', new FixedTimezone('+0100'))->timezone()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     *
     * @return void
     */
    #[TestWith(['946728000.000000', '2000-01-01 12:00:00'])]
    public function testTimestamp (string $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->timestamp()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $value
     * @param \FireHub\Core\Type\Date\Zone $zone
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['01.01.2000 12.00.00', Zone::ARCTIC_LONGYEARBYEN])]
    public function testWithTimezone (string $value, Zone $zone):void {

        self::assertEquals(new NamedTimezone($zone), DateTime::from($value)->withTimezone(new NamedTimezone($zone))->timezone());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Format\Token $format
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01T12:00:00.000+00:00', '2000-01-01 12:00:00', Format::ATOM_EXTENDED])]
    #[TestWith(['12:00:00', '2000-01-01 12:00:00', 'H:i:s'])]
    public function testFormat (string $expected, string $value, string|Token $format):void {

        self::assertSame($expected, DateTime::from($value)->format($format));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([true, '2000-01-01 12:00:00'])]
    public function testIsLeapYear (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isLeapYear());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([2, '2000-01-01 12:00:00'])]
    public function testMillenniumIndex (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->millenniumIndex());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([3, '2000-01-01 12:00:00'])]
    public function testMillennium(int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->millennium());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([20, '2000-01-01 12:00:00'])]
    public function testCenturyIndex (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->centuryIndex());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([21, '2000-01-01 12:00:00'])]
    public function testCentury (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->century());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([200, '2000-01-01 12:00:00'])]
    public function testDecadeIndex (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->decadeIndex());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([201, '2000-01-01 12:00:00'])]
    public function testDecade (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->decade());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([2000, '2000-01-01 12:00:00'])]
    public function testYear (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->year());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([0, '2000-01-01 12:00:00'])]
    public function testYearShort (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->yearShort());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([1, '2000-01-01 12:00:00'])]
    #[TestWith([1, '2000-03-31 12:00:00'])]
    #[TestWith([2, '2000-04-01 12:00:00'])]
    #[TestWith([2, '2000-06-30 12:00:00'])]
    #[TestWith([3, '2000-07-01 12:00:00'])]
    #[TestWith([3, '2000-09-30 12:00:00'])]
    #[TestWith([4, '2000-10-01 12:00:00'])]
    #[TestWith([4, '2000-12-31 12:00:00'])]
    public function testQuarter (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->quarter());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([Month::JANUARY, '2000-01-01 12:00:00'])]
    public function testMonth (Month $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->month());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['01', '2000-01-01 12:00:00'])]
    public function testMonthPadded (string $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->monthPadded());

    }

    /**
     * @since 1.0.0
     *
     * @param int<1,31> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([31, '2000-01-01 12:00:00'])]
    public function testDaysInMonth (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->daysInMonth());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([1, '2000-01-01 12:00:00'])]
    #[TestWith([1, '2000-01-14 12:00:00'])]
    #[TestWith([2, '2000-01-15 12:00:00'])]
    #[TestWith([2, '2000-01-28 12:00:00'])]
    #[TestWith([3, '2000-01-29 12:00:00'])]
    #[TestWith([27, '2000-12-31 12:00:00'])]
    public function testFortnight (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->fortnight());

    }

    /**
     * @since 1.0.0
     *
     * @param int<1,53> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([52, '2000-01-01 12:00:00'])]
    public function testWeekOfYear (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->weekOfYear());

    }

    /**
     * @since 1.0.0
     *
     * @param int<1,366> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([1, '2000-01-01 12:00:00'])]
    public function testDayOfYear (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->dayOfYear());

    }

    /**
     * @since 1.0.0
     *
     * @param int<1,31> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([1, '2000-01-01 12:00:00'])]
    public function testDayOfMonth (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->dayOfMonth());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Core\Meta\Enum\Date\WeekDay $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([WeekDay::SATURDAY, '2000-01-01 12:00:00'])]
    public function testWeekDay (WeekDay $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->weekDay());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['01', '2000-01-01 12:00:00'])]
    public function testDayPadded (string $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->dayPadded());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['st', '2000-01-01 12:00:00'])]
    public function testDayOrdinalSuffix (string $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->dayOrdinalSuffix());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,24> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([18, '2000-01-01 18:00:00'])]
    public function testHour (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->hour());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['18', '2000-01-01 18:00:00'])]
    public function testHourPadded (string $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->hourPadded());

    }

    /**
     * @since 1.0.0
     *
     * @param int<1,12> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([6, '2000-01-01 18:00:00'])]
    public function testHour12 (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->hour12());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['06', '2000-01-01 18:00:00'])]
    public function testHour12Padded (string $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->hour12Padded());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['PM', '2000-01-01 18:00:00'])]
    public function testMeridiem (string $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->meridiem());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['pm', '2000-01-01 18:00:00'])]
    public function testMeridiemLower (string $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->meridiemLower());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,59> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([13, '2000-01-01 12:13:14.123888'])]
    public function testMinute (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->minute());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,59> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([14, '2000-01-01 12:13:14.123888'])]
    public function testSecond (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->second());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,999> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([123, '2000-01-01 12:13:14.123888'])]
    public function testMillisecond (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->millisecond());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,999999> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([123888, '2000-01-01 12:13:14.123888'])]
    public function testMicrosecond (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->microsecond());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,999> $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([550, '2000-01-01 12:13:14.123888'])]
    public function testSwatchBeat (int $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->swatchBeat());

    }

}