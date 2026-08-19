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
     * @param numeric-string $expected
     * @param float $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     *
     * @return void
     */
    #[TestWith(['10.2', 10.2])]
    public function testToDecimal (string $expected, float $value):void {

        self::assertSame($expected, new Real($value)->toDecimal()->value());

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

}