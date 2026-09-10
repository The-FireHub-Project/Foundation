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
use FireHub\Core\Meta\Enum\Date\Epoch;
use FireHub\Foundation\Temporal\UnixTimestamp;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable Unix timestamp value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('temporal')]
#[CoversClass(UnixTimestamp::class)]
final class UnixTimestampTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Epoch $epoch
     * @param int $seconds
     * @param int<0,999999> $fraction
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Runtime\Exception\EmptyPadException
     *
     * @return void
     */
    #[TestWith(['315964900.000245', Epoch::GPS, 100, 245])]
    #[TestWith(['946728100.000245', '2000-01-01 12:00:00', 100, 245])]
    public function testFromEpoch (string $expected, string|Epoch $epoch, int $seconds, int $fraction = 0):void {

        self::assertSame($expected ,UnixTimestamp::fromEpoch($epoch, $seconds, $fraction)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param int $seconds
     * @param int<0,999999> $fraction
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException
     * @throws \FireHub\Runtime\Exception\EmptyPadException
     *
     * @return void
     */
    #[TestWith(['0.000000', 0])]
    #[TestWith(['100.000245', 100, 245])]
    public function testValue (string $expected, int $seconds, int $fraction = 0):void {

        self::assertSame($expected, new UnixTimestamp($seconds, $fraction)->value());

    }

}