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
use FireHub\Foundation\Temporal\FixedTimezone;
use FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable fixed-offset timezone value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(FixedTimezone::class)]
final class FixedTimezoneTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $zone
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     *
     * @return void
     */
    #[TestWith(['+05:30', '+05:30'])]
    #[TestWith(['-05:30', '-0530'])]
    public function testValue (string $expected, string $zone):void {

        self::assertSame($expected, new FixedTimezone($zone)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param non-empty-string $zone
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException
     *
     * @return void
     */
    #[TestWith([-3600, '-0100'])]
    #[TestWith([3600, '+0100'])]
    public function testOffset (int $expected, string $zone):void {

        self::assertSame($expected, new FixedTimezone($zone)->offset());

    }

}