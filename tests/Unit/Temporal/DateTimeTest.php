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
use FireHub\Core\Type\Temporal\Timezone;
use FireHub\Core\Meta\Enum\Date\Format;
use FireHub\Foundation\Temporal\DateTime;
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
     * @param null|\FireHub\Core\Type\Temporal\Timezone $timezone
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.000000', '2000-01-01 12:00:00'])]
    public function testFrom (string $expected, string $value, ?Timezone $timezone = null):void {

        self::assertSame($expected, DateTime::from($value, $timezone)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Format $format
     * @param null|\FireHub\Core\Type\Temporal\Timezone<non-empty-string> $timezone
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     *
     * @return void
     */
    #[TestWith(['2000-01-01 12:00:00.000000', '01.01.2000 12.00.00', 'd.m.Y H.i.s'])]
    public function testFromFormat (string $expected, string $value, string|Format $format = Format::ISO_DATE_TIME, ?Timezone $timezone = null):void {

        self::assertSame($expected, DateTime::fromFormat($value, $format, $timezone)->value());

    }

}