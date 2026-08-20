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

namespace FireHub\Tests\Foundation\Unit\Number;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Number\Integer;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable integer value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('number')]
#[CoversClass(Integer::class)]
final class IntegerTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param mixed $value
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([1, '1'])]
    public function testOf (int $expected, mixed $value):void {

        self::assertSame($expected, Integer::of($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param int $value
     *
     * @return void
     */
    #[TestWith([10.0, 10])]
    public function testToReal (float $expected, int $value):void {

        self::assertSame($expected, new Integer($value)->toReal()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param int $value
     *
     * @return void
     */
    #[TestWith([10, 10])]
    public function testValue (int $expected, int $value):void {

        self::assertSame($expected, new Integer($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param int $value
     * @param int|\FireHub\Foundation\Number\Integer $added_value
     *
     * @return void
     */
    #[TestWith([30, 10, 20])]
    public function testAdd (int $expected, int $value, int|Integer $added_value):void {

        self::assertSame($expected, new Integer($value)->add($added_value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param int $value
     * @param int|\FireHub\Foundation\Number\Integer $added_value
     *
     * @return void
     */
    #[TestWith([-10, 10, 20])]
    public function testSubtract (int $expected, int $value, int|Integer $added_value):void {

        self::assertSame($expected, new Integer($value)->subtract($added_value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param int $value
     * @param int|\FireHub\Foundation\Number\Integer $added_value
     *
     * @return void
     */
    #[TestWith([200, 10, 20])]
    public function testMultiply (int $expected, int $value, int|Integer $added_value):void {

        self::assertSame($expected, new Integer($value)->multiply($added_value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param int $value
     * @param int|\FireHub\Foundation\Number\Integer $added_value
     *
     * @return void
     */
    #[TestWith([5, 10, 2])]
    public function testDivide (int $expected, int $value, int|Integer $added_value):void {

        self::assertSame($expected, new Integer($value)->divide($added_value)->value());

    }

}