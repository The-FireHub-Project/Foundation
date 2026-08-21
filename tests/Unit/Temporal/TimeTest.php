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
use FireHub\Core\Meta\Enum\Date\Format;
use FireHub\Foundation\Temporal\Time;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable time value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(Time::class)]
final class TimeTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param non-empty-string $expected
     * @param non-empty-string $value
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Format $format
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeException
     *
     * @return void
     */
    #[TestWith(['12:00:00.000000', '12:00:00'])]
    #[TestWith(['12:00:00.000000', '12.00.00.000000', 'H.i.s.u'])]
    public function testFrom (string $expected, string $value, string|Format $format = Format::ISO_TIME):void {

        self::assertSame($expected, Time::from($value, $format)->value());

    }

}