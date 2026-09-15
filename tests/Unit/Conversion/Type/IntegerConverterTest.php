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
use FireHub\Foundation\Conversion\Type\IntegerConverter;
use FireHub\Testing\Stubs\EmptyClass;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Converts values into integers
 * @since 1.0.0
 */
#[Small]
#[Group('conversion')]
#[CoversClass(IntegerConverter::class)]
final class IntegerConverterTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param null|int $expected
     * @param mixed $value
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([10, '10'])]
    #[TestWith([null, '10.12'])]
    #[TestWith([null, '-1.1'])]
    #[TestWith([0, '0'])]
    #[TestWith([10, 10])]
    #[TestWith([null, 10.12])]
    #[TestWith([null, -1.1])]
    #[TestWith([0, 0])]
    #[TestWith([null, 'off'])]
    #[TestWith([null, null])]
    #[TestWith([null, 'firehub'])]
    #[TestWith([null, new EmptyClass])]
    public function testConvert (?int $expected, mixed $value):void {

        self::assertSame($expected, new IntegerConverter($value)->convert());

    }

}