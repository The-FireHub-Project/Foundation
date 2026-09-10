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

}