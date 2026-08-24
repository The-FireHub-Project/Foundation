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
use FireHub\Foundation\Temporal\NamedTimezone;
use FireHub\Core\Type\Date\Zone;
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

}