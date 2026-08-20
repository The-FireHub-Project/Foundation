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
use FireHub\Core\Meta\Enum\Number\Format;
use FireHub\Foundation\Number\Real;
use FireHub\Foundation\Number\Exception\InvalidConversionException;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable real number value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('number')]
#[CoversClass(Real::class)]
final class RealTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param mixed $value
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException
     *
     * @return void
     */
    #[TestWith([1.1, '1.1'])]
    public function testOf (float $expected, mixed $value):void {

        self::assertSame($expected, Real::of($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param string $value
     * @param string $decimal_separator
     * @param string $thousands_separator
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([1234.56, '1.234,56', ',', '.'])]
    public function testFromFormat (float $expected, string $value, string $decimal_separator = '.', string $thousands_separator = ','):void {

        self::assertSame(
            $expected,
            Real::fromFormat(
                $value,
                $decimal_separator,
                $thousands_separator
            )->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param numeric-string $value
     * @param \FireHub\Core\Meta\Enum\Number\Format $format
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([1234.56, '1 234.56', Format::SI])]
    public function testFromStandard (float $expected, string $value, Format $format):void {

        self::assertSame($expected,  Real::fromStandard($value, $format)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param float $value
     *
     * @return void
     */
    #[TestWith([true, 10.0])]
    public function testIsFinite (bool $expected, float $value):void {

        self::assertSame($expected, new Real($value)->isFinite());

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param float $value
     *
     * @return void
     */
    #[TestWith([false, 10.0])]
    public function testIsInfinite (bool $expected, float $value):void {

        self::assertSame($expected, new Real($value)->isInfinite());

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param float $value
     *
     * @return void
     */
    #[TestWith([false, 10.0])]
    public function testIsNaN (bool $expected, float $value):void {

        self::assertSame($expected, new Real($value)->isNaN());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param float $value
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidConversionException
     *
     * @return void
     */
    #[TestWith([10, 10.0])]
    public function testToInteger (int $expected, float $value):void {

        self::assertSame($expected, new Real($value)->toInteger()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param float $value
     *
     * @return void
     */
    #[TestWith([10.1])]
    public function testToIntegerInvalid (float $value):void {

        $this->expectException(InvalidConversionException::class);

        new Real($value)->toInteger();

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param float $value
     *
     * @return void
     */
    #[TestWith([9.99, 9.99])]
    public function testValue (float $expected, float $value):void {

        self::assertSame($expected, new Real($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param float $value
     * @param float|\FireHub\Foundation\Number\Real $added_value
     *
     * @return void
     */
    #[TestWith([12.2, 10.2, 2.0])]
    public function testAdd (float $expected, float $value, float|Real $added_value):void {

        self::assertSame($expected, new Real($value)->add($added_value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param float $value
     * @param float|\FireHub\Foundation\Number\Real $added_value
     *
     * @return void
     */
    #[TestWith([8.2, 10.2, 2.0])]
    public function testSubtract (float $expected, float $value, float|Real $added_value):void {

        self::assertSame($expected, new Real($value)->subtract($added_value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param float $value
     * @param float|\FireHub\Foundation\Number\Real $added_value
     *
     * @return void
     */
    #[TestWith([21, 10.5, 2])]
    public function testMultiply (float $expected, float $value, float|Real $added_value):void {

        self::assertSame($expected, new Real($value)->multiply($added_value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param float $value
     * @param float|\FireHub\Foundation\Number\Real $added_value
     *
     * @return void
     */
    #[TestWith([2.5, 10.0, 4.0])]
    public function testDivide (float $expected, float $value, float|Real $added_value):void {

        self::assertSame($expected, new Real($value)->divide($added_value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param float $value
     * @param float|int|\FireHub\Foundation\Number\Real $exponent
     *
     * @return void
     */
    #[TestWith([6.25, 2.5, 2])]
    public function testPower (float $expected, float $value, float|int|Real $exponent):void {

        self::assertSame($expected, new Real($value)->power($exponent)->value());

    }

}