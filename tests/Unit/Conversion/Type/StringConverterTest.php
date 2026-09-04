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

namespace FireHub\Tests\Foundation\Unit\Type;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Conversion\Type\StringConverter;
use FireHub\Tests\Foundation\Stubs\ {
    BackedEnum, UnitEnum
};
use FireHub\Testing\Stubs\ {
    EmptyClass, StringableClass
};
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Converts values into strings
 * @since 1.0.0
 */
#[Small]
#[Group('conversion')]
#[CoversClass(StringConverter::class)]
final class StringConverterTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param null|string $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith(['10', '10'])]
    #[TestWith(['10.12', '10.12'])]
    #[TestWith(['-1.1', '-1.1'])]
    #[TestWith(['0', 0])]
    #[TestWith(['20', 20])]
    #[TestWith([null, null])]
    #[TestWith(['1', true])]
    #[TestWith(['', false])]
    #[TestWith(['off', 'off'])]
    #[TestWith([null, new EmptyClass])]
    #[TestWith(['FireHub', new StringableClass])]
    #[TestWith(['test', BackedEnum::A])]
    #[TestWith(['A', UnitEnum::A])]
    public function testConvert (?string $expected, mixed $value):void {

        self::assertSame($expected, new StringConverter($value)->convert());

    }

}