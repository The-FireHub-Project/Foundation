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
    DateTime, NamedTimezone, FixedTimezone, Timespan
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
     * @param bool $expected
     * @param non-empty-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([false, '2000-01-01 12:00:00'])]
    public function testIsDTS (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isDST());

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
    #[TestWith([false, '2000-01-01 12:00:00'])]
    public function testIsToday (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isToday());

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
    #[TestWith([false, '2000-01-01 12:00:00'])]
    public function testIsYesterday (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isYesterday());

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
    #[TestWith([false, '2000-01-01 12:00:00'])]
    public function testIsTomorrow (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isTomorrow());

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
    public function testIsFirstDayOfMonth (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isFirstDayOfMonth());

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
    #[TestWith([false, '2000-01-01 12:00:00'])]
    public function testIsLastDayOfMonth (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isLastDayOfMonth());

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
    public function testIsFirstDayOfYear (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isFirstDayOfYear());

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
    #[TestWith([false, '2000-01-01 12:00:00'])]
    public function testIsLastDayOfYear (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isLastDayOfYear());

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
    public function testIsWeekend (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isWeekend());

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
    #[TestWith([false, '2000-01-01 12:00:00'])]
    public function testIsWeekday (bool $expected, string $value):void {

        self::assertSame($expected, DateTime::from($value)->isWeekday());

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param non-empty-string $datetime
     * @param non-empty-string $datetime2
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([false, '2000-01-01 12:00:00.000000', '2000-01-01 12:00:00.000001'])]
    public function testIsEqual (bool $expected, string $datetime, string $datetime2):void {

        self::assertSame($expected, DateTime::from($datetime)->isEqual(DateTime::from($datetime2)));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param non-empty-string $datetime
     * @param non-empty-string $datetime2
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([true, '2000-01-01 12:00:00.000000', '2000-01-01 12:00:00.000001'])]
    public function testIsBefore (bool $expected, string $datetime, string $datetime2):void {

        self::assertSame($expected, DateTime::from($datetime)->isBefore(DateTime::from($datetime2)));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param non-empty-string $datetime
     * @param non-empty-string $datetime2
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith([false, '2000-01-01 12:00:00.000000', '2000-01-01 12:00:00.000001'])]
    public function testIsAfter (bool $expected, string $datetime, string $datetime2):void {

        self::assertSame($expected, DateTime::from($datetime)->isAfter(DateTime::from($datetime2)));

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

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<-9999,9999> $year
     * @param int<1,12> $month
     * @param int<1,31> $day
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2026-01-01 12:00:00.123456', '2000-01-01 12:00:00.123456', 2026, 1, 1])]
    public function testWithDate (string $expected, string $value, int $year, int $month, int $day):void {

        self::assertSame($expected, DateTime::from($value)->withDate($year, $month, $day)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<0,23> $hour
     * @param int<0,59> $minute
     * @param int<0,59> $second
     * @param int<0,999999> $microsecond
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 13:01:01.111111', '2000-01-01 12:00:00.123456', 13, 1, 1, 111111])]
    public function testWithTime (string $expected, string $value, int $hour, int $minute, int $second, int $microsecond):void {

        self::assertSame($expected, DateTime::from($value)->withTime($hour, $minute, $second, $microsecond)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<-9999,9999> $year
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2026-01-01 12:00:00.123456', '2000-01-01 12:00:00.123456', 2026])]
    public function testWithYear (string $expected, string $value, int $year):void {

        self::assertSame($expected, DateTime::from($value)->withYear($year)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<1,12> $month
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-06-01 12:00:00.123456', '2000-01-01 12:00:00.123456', 6])]
    public function testWithMonth (string $expected, string $value, int $month):void {

        self::assertSame($expected, DateTime::from($value)->withMonth($month)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<1,31> $day
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-15 12:00:00.123456', '2000-01-01 12:00:00.123456', 15])]
    public function testWithDay (string $expected, string $value, int $day):void {

        self::assertSame($expected, DateTime::from($value)->withDay($day)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<0,23> $hour
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 13:00:00.123456', '2000-01-01 12:00:00.123456', 13])]
    public function testWithHour (string $expected, string $value, int $hour):void {

        self::assertSame($expected, DateTime::from($value)->withHour($hour)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<0,59> $minute
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:30:00.123456', '2000-01-01 12:00:00.123456', 30])]
    public function testWithMinute (string $expected, string $value, int $minute):void {

        self::assertSame($expected, DateTime::from($value)->withMinute($minute)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<0,59> $second
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:30.123456', '2000-01-01 12:00:00.123456', 30])]
    public function testWithSecond (string $expected, string $value, int $second):void {

        self::assertSame($expected, DateTime::from($value)->withSecond($second)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param int<0,999999> $microsecond
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.654321', '2000-01-01 12:00:00.123456', 654321])]
    public function testWithMicrosecond (string $expected, string $value, int $microsecond):void {

        self::assertSame($expected, DateTime::from($value)->withMicrosecond($microsecond)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param non-empty-string $ticks
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     *
     * @return void
     */
    #[TestWith(['2000-01-05 11:20:00.000002', '2000-01-01 00:00:00.000000', '386400000002'])]
    public function testAdd (string $expected, string $value, string $ticks):void {

        self::assertSame(
            $expected,
            DateTime::from($value)->add(new Timespan($ticks))->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param non-empty-string $ticks
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     *
     * @return void
     */
    #[TestWith(['1999-12-27 12:39:59.999998', '2000-01-01 00:00:00.000000', '386400000002'])]
    public function testSub (string $expected, string $value, string $ticks):void {

        self::assertSame(
            $expected,
            DateTime::from($value)->sub(new Timespan($ticks))->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param array $expected
     * @param non-empty-string $datetime
     * @param non-empty-string $datetime2
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith([['days' => '1', 'hours' => 12, 'minutes' => 0, 'seconds' => 0, 'microseconds' => 123456], '2000-01-01 00:00:00.000000', '2000-01-02 12:00:00.123456'])]
    public function testDiff (array $expected, string $datetime, string $datetime2):void {

        self::assertSame(
            $expected,
            DateTime::from($datetime)
                ->diff(DateTime::from($datetime2))->components()->value()
        );

    }

}