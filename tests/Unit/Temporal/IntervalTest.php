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
    DateTime, Interval, Period, Timespan
};
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable temporal Interval Value Object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(Interval::class)]
final class IntervalTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param bool $start_inclusive
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith([true, true])]
    #[TestWith([false, false])]
    public function testIsStartInclusive (bool $expected, bool $start_inclusive):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from('2000-01-01 12:00:00'), DateTime::from('3000-01-01 12:00:00')),
                new Timespan('0')->addDays('1'),
                $start_inclusive
            )->isStartInclusive()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param bool $end_inclusive
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith([true, true])]
    #[TestWith([false, false])]
    public function testIsEndInclusive (bool $expected, bool $end_inclusive):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from('2000-01-01 12:00:00'), DateTime::from('3000-01-01 12:00:00')),
                new Timespan('0')->addDays('1'),
                false, $end_inclusive
            )->isEndInclusive()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $start
     * @param string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith([false, '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testIsEmpty (bool $expected, string $start, string $end):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from($start), DateTime::from($end)),
                new Timespan('0')->addDays('1')
            )->isEmpty()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param non-negative-int $expected
     * @param string $start
     * @param string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith([7792, '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testCount (int $expected, string $start, string $end):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from($start), DateTime::from($end)),
                new Timespan('0')->addDays('1')
            )->count()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $start
     * @param string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.111111', '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testFirst (string $expected, string $start, string $end):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from($start), DateTime::from($end)),
                new Timespan('0')->addDays('1')
            )->first()->value()->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $start
     * @param string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith(['2021-05-01 12:00:00.111111', '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testLast (string $expected, string $start, string $end):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from($start), DateTime::from($end)),
                new Timespan('0')->addDays('1')
            )->last()->value()->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param non-negative-int $at
     * @param string $start
     * @param string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith(['2000-01-02 12:00:00.111111', 1, '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testAt (string $expected, int $at, string $start, string $end):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from($start), DateTime::from($end)),
                new Timespan('0')->addDays('1')
            )->at($at)->value()->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $contains
     * @param string $start
     * @param string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith([true, '2000-05-01 12:00:00.111111', '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testContains (bool $expected, string $contains, string $start, string $end):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from($start), DateTime::from($end)),
                new Timespan('0')->addDays('1')
            )->contains(DateTime::from($contains))
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $datetime
     * @param string $start
     * @param string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith(['2000-05-02 12:00:00.111111', '2000-05-01 12:00:00.111111', '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testNext (string $expected, string $datetime, string $start, string $end):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from($start), DateTime::from($end)),
                new Timespan('0')->addDays('1')
            )->next(DateTime::from($datetime))->value()->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $datetime
     * @param string $start
     * @param string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith(['2000-04-30 12:00:00.111111', '2000-05-01 12:00:00.111111', '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testPrevious (string $expected, string $datetime, string $start, string $end):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from($start), DateTime::from($end)),
                new Timespan('0')->addDays('1')
            )->previous(DateTime::from($datetime))->value()->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param array $expected
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks
     *
     * @return void
     */
    #[TestWith([[
        'period' => '2000-01-01 12:00:00.111111 - 2021-05-01 18:30:00.123456',
        'timespan' => '86400000000',
        'start_inclusive' => true,
        'end_inclusive' => true
    ]])]
    public function testValue (array $expected):void {

        self::assertSame(
            $expected,
            new Interval(
                new Period(DateTime::from('2000-01-01 12:00:00.111111'), DateTime::from('2021-05-01 18:30:00.123456')),
                new Timespan('0')->addDays('1')
            )->value()
        );

    }

}