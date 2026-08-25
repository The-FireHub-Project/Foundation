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
use FireHub\Core\Type\Geo\Country;
use FireHub\Foundation\Temporal\NamedTimezone;
use FireHub\Foundation\Temporal\DateTime;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable named timezone value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(NamedTimezone::class)]
final class NamedTimezoneTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param \FireHub\Core\Type\Date\Zone $zone
     *
     * @return void
     */
    #[TestWith(['Arctic/Longyearbyen', Zone::ARCTIC_LONGYEARBYEN])]
    public function testValue (string $expected, Zone $zone):void {

        self::assertSame($expected, new NamedTimezone($zone)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Geo\Country $expected
     * @param \FireHub\Core\Type\Date\Zone $zone
     *
     * @return void
     */
    #[TestWith([Country::SVALBARD_AND_JAN_MAYEN, Zone::ARCTIC_LONGYEARBYEN])]
    public function test (Country $expected, Zone $zone):void {

        self::assertSame($expected, new NamedTimezone($zone)->country());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param \FireHub\Core\Type\Date\Zone $zone
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime string is invalid.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     *
     * @return void
     */
    #[TestWith([3600, Zone::ARCTIC_LONGYEARBYEN])]
    public function testOffset (int $expected, Zone $zone):void {

        self::assertSame($expected, new NamedTimezone($zone)->offset(DateTime::from('2000-01-01')));

    }

}