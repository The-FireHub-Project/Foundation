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
use FireHub\Foundation\Conversion\Type\BooleanConverter;
use FireHub\Testing\Stubs\EmptyClass;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Converts values into booleans
 * @since 1.0.0
 */
#[Small]
#[Group('conversion')]
#[CoversClass(BooleanConverter::class)]
final class BooleanConverterTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param null|bool $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([true, true])]
    #[TestWith([false, false])]
    #[TestWith([true, 'true'])]
    #[TestWith([true, 'True'])]
    #[TestWith([true, '1'])]
    #[TestWith([true, 'yes'])]
    #[TestWith([true, 'on'])]
    #[TestWith([false, 'false'])]
    #[TestWith([false, '0'])]
    #[TestWith([false, 'no'])]
    #[TestWith([false, 'off'])]
    #[TestWith([null, null])]
    #[TestWith([null, 'firehub'])]
    #[TestWith([null, new EmptyClass])]
    public function testConvert (?bool $expected, mixed $value):void {

        self::assertSame($expected, new BooleanConverter($value)->convert());

    }

}