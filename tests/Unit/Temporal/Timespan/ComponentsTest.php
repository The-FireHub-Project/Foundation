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

namespace FireHub\Tests\Foundation\Unit\Temporal\Timespan;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Temporal\Timespan\Components;
use FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable temporal Timespan components with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(Components::class)]
final class ComponentsTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param numeric-string $days
     * @param int<0,23> $hours
     * @param int<0,59> $minutes
     * @param int<0,59> $seconds
     * @param int<0,999999> $microseconds
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     *
     * @return void
     */
    #[TestWith(['-1', 1, 1, 1, 1])]
    #[TestWith(['1', -1, 1, 1, 1])]
    #[TestWith(['1', 1, -1, 1, 1])]
    #[TestWith(['1', 1, 1, -1, 1])]
    #[TestWith(['1', 1, 1, 1, -1])]
    public function testCreateException (string $days, int $hours, int $minutes, int $seconds, int $microseconds):void {

        $this->expectException(InvalidTimespanComponents::class);

        new Components($days, $hours, $minutes, $seconds, $microseconds);

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $days
     * @param int<0,23> $hours
     * @param int<0,59> $minutes
     * @param int<0,59> $seconds
     * @param int<0,999999> $microseconds
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents
     *
     * @return void
     */
    #[TestWith(['1', '1', 1, 1, 1, 1])]
    public function testDays (string $expected, string $days, int $hours, int $minutes, int $seconds, int $microseconds):void {

        self::assertSame($expected, new Components($days, $hours, $minutes, $seconds, $microseconds)->days());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,23> $expected
     * @param numeric-string $days
     * @param int<0,23> $hours
     * @param int<0,59> $minutes
     * @param int<0,59> $seconds
     * @param int<0,999999> $microseconds
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents
     *
     * @return void
     */
    #[TestWith([1, '1', 1, 1, 1, 1])]
    public function testHours (int $expected, string $days, int $hours, int $minutes, int $seconds, int $microseconds):void {

        self::assertSame($expected, new Components($days, $hours, $minutes, $seconds, $microseconds)->hours());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,59> $expected
     * @param numeric-string $days
     * @param int<0,23> $hours
     * @param int<0,59> $minutes
     * @param int<0,59> $seconds
     * @param int<0,999999> $microseconds
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents
     *
     * @return void
     */
    #[TestWith([1, '1', 1, 1, 1, 1])]
    public function testMinutes (int $expected, string $days, int $hours, int $minutes, int $seconds, int $microseconds):void {

        self::assertSame($expected, new Components($days, $hours, $minutes, $seconds, $microseconds)->minutes());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,59> $expected
     * @param numeric-string $days
     * @param int<0,23> $hours
     * @param int<0,59> $minutes
     * @param int<0,59> $seconds
     * @param int<0,999999> $microseconds
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents
     *
     * @return void
     */
    #[TestWith([1, '1', 1, 1, 1, 1])]
    public function testSeconds (int $expected, string $days, int $hours, int $minutes, int $seconds, int $microseconds):void {

        self::assertSame($expected, new Components($days, $hours, $minutes, $seconds, $microseconds)->seconds());

    }

    /**
     * @since 1.0.0
     *
     * @param int<0,999999> $expected
     * @param numeric-string $days
     * @param int<0,23> $hours
     * @param int<0,59> $minutes
     * @param int<0,59> $seconds
     * @param int<0,999999> $microseconds
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents
     *
     * @return void
     */
    #[TestWith([1, '1', 1, 1, 1, 1])]
    public function testMicroseconds (int $expected, string $days, int $hours, int $minutes, int $seconds, int $microseconds):void {

        self::assertSame($expected, new Components($days, $hours, $minutes, $seconds, $microseconds)->microseconds());

    }

    /**
     * @since 1.0.0
     *
     * @param array<array-key, mixed> $expected
     * @param numeric-string $days
     * @param int<0,23> $hours
     * @param int<0,59> $minutes
     * @param int<0,59> $seconds
     * @param int<0,999999> $microseconds
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents
     *
     * @return void
     */
    #[TestWith([['days' => '1', 'hours' => 1, 'minutes' => 1, 'seconds' => 1, 'microseconds' => 1], '1', 1, 1, 1, 1])]
    #[TestWith([
        [
            'days' => '365',
            'hours' => 12,
            'minutes' => 30,
            'seconds' => 45,
            'microseconds' => 123456
        ],
        '365', 12, 30, 45, 123456
    ])]
    #[TestWith([
        [
            'days' => '999999999999999999999999999999',
            'hours' => 23,
            'minutes' => 59,
            'seconds' => 59,
            'microseconds' => 999999
        ],
        '999999999999999999999999999999', 23, 59, 59, 999999
    ])]
    #[TestWith([
        [
            'days' => '100000000000000000000000000000000000000',
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 500000
        ],
        '100000000000000000000000000000000000000', 0, 0, 0, 500000
    ])]
    #[TestWith([
        [
            'days' => '42',
            'hours' => 23,
            'minutes' => 59,
            'seconds' => 59,
            'microseconds' => 999999
        ],
        '42', 23, 59, 59, 999999
    ])]
    public function testValue (array $expected, string $days, int $hours, int $minutes, int $seconds, int $microseconds):void {

        self::assertSame($expected, new Components($days, $hours, $minutes, $seconds, $microseconds)->value());

    }

}