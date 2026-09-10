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

namespace FireHub\Tests\Foundation\Unit;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Boolean;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable boolean value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('boolean')]
#[CoversClass(Boolean::class)]
final class BooleanTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param mixed $value
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException
     *
     * @return void
     */
    #[TestWith([true, 1])]
    #[TestWith([false, 0])]
    #[TestWith([true, '1'])]
    #[TestWith([false, '0'])]
    #[TestWith([true, 'yes'])]
    #[TestWith([false, 'no'])]
    #[TestWith([true, 'on'])]
    #[TestWith([false, 'off'])]
    public function testOf (bool $expected, mixed $value):void {

        self::assertSame($expected, Boolean::of($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param bool $value
     *
     * @return void
     */
    #[TestWith([true, true])]
    public function testValue (bool $expected, bool $value):void {

        self::assertSame($expected, new Boolean($value)->value());

    }

}