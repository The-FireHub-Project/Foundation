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
use FireHub\Foundation\Conversion\Type\ArrayConverter;
use FireHub\Tests\Foundation\Stubs\ {
    Iterator, JsonSerializable
};
use FireHub\Testing\Stubs\EmptyClass;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Converts values into arrays
 * @since 1.0.0
 */
#[Small]
#[Group('conversion')]
#[CoversClass(ArrayConverter::class)]
final class ArrayConverterTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param null|array<array-key, mixed> $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([null, null])]
    #[TestWith([['x', 'y', 'z'], ['x', 'y', 'z']])]
    #[TestWith([null, 'firehub'])]
    #[TestWith([[1, 2, 3], new Iterator])]
    #[TestWith([[10], new JsonSerializable])]
    #[TestWith([null, new EmptyClass])]
    public function testConvert (?array $expected, mixed $value):void {

        self::assertSame($expected, new ArrayConverter($value)->convert());

    }

}