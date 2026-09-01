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
    DateTime, Period, Timespan
};
use FireHub\Foundation\Temporal\Exception\InvalidPeriodException;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable temporal Period Value Object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(Period::class)]
final class PeriodTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $start
     * @param non-empty-string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     *
     * @return void
     */
    #[TestWith(['2001-01-01 12:00:00', '2000-01-01 12:00:00'])]
    public function testInvalidPeriod (string $start, string $end):void {

        $this->expectException(InvalidPeriodException::class);

        new Period(DateTime::from($start), DateTime::from($end));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param non-empty-string $start
     * @param non-empty-string $end
     * @param non-empty-string $datetime
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     *
     * @return void
     */
    #[TestWith([true, '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456', '2000-01-01 12:00:00.111111'])]
    #[TestWith([false, '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456', '2000-01-01 12:00:00.111110'])]
    #[TestWith([false, '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456', '2021-05-01 18:30:00.123457'])]
    public function testInPeriod (bool $expected, string $start, string $end, string $datetime):void {

        self::assertSame($expected, new Period(DateTime::from($start), DateTime::from($end))->inPeriod(DateTime::from($datetime)));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param non-empty-string $start
     * @param non-empty-string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     *
     * @return void
     */
    #[TestWith([false, '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testInProgress (bool $expected, string $start, string $end,):void {

        self::assertSame($expected, new Period(DateTime::from($start), DateTime::from($end))->inProgress());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $start
     * @param non-empty-string $end
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.111111 - 2021-05-01 18:30:00.123456', '2000-01-01 12:00:00.111111', '2021-05-01 18:30:00.123456'])]
    public function testValue (string $expected, string $start, string $end):void {

        self::assertSame($expected, new Period(DateTime::from($start), DateTime::from($end))->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $start
     * @param non-empty-string $end
     * @param numeric-string $timespan
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     *
     * @return void
     */
    #[TestWith(['1999-12-30 23:59:59.876544 - 2021-01-01 12:00:00.000000', '2000-01-01 12:00:00.000000', '2021-01-01 12:00:00.000000', '129600123456'])]
    public function testReduceStart (string $expected, string $start, string $end, string $timespan):void {

        self::assertSame(
            $expected,
            new Period(
                DateTime::from($start),
                DateTime::from($end)
            )->reduceStart(new Timespan($timespan))->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $start
     * @param non-empty-string $end
     * @param numeric-string $timespan
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     *
     * @return void
     */
    #[TestWith(['2000-01-03 00:00:00.123456 - 2021-01-01 12:00:00.000000', '2000-01-01 12:00:00.000000', '2021-01-01 12:00:00.000000', '129600123456'])]
    public function testExtendStart (string $expected, string $start, string $end, string $timespan):void {

        self::assertSame(
            $expected,
            new Period(
                DateTime::from($start),
                DateTime::from($end)
            )->extendStart(new Timespan($timespan))->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $start
     * @param non-empty-string $end
     * @param numeric-string $timespan
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.000000 - 2020-12-30 23:59:59.876544', '2000-01-01 12:00:00.000000', '2021-01-01 12:00:00.000000', '129600123456'])]
    public function testReduceEnd (string $expected, string $start, string $end, string $timespan):void {

        self::assertSame(
            $expected,
            new Period(
                DateTime::from($start),
                DateTime::from($end)
            )->reduceEnd(new Timespan($timespan))->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $start
     * @param non-empty-string $end
     * @param numeric-string $timespan
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.000000 - 2021-01-03 00:00:00.123456', '2000-01-01 12:00:00.000000', '2021-01-01 12:00:00.000000', '129600123456'])]
    public function testExtendEnd (string $expected, string $start, string $end, string $timespan):void {

        self::assertSame(
            $expected,
            new Period(
                DateTime::from($start),
                DateTime::from($end)
            )->extendEnd(new Timespan($timespan))->value());

    }

}