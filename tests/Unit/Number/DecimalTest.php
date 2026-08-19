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
use FireHub\Foundation\Number\Decimal;
use FireHub\Foundation\Number\Exception\ {
    InvalidConversionException, InvalidFractionalException
};
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable decimal value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('number')]
#[CoversClass(Decimal::class)]
final class DecimalTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testInvalidDecimal ():void {

        $this->expectException(InvalidFractionalException::class);

        new Decimal('test');

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param string $value
     * @param string $decimal_separator
     * @param string $thousands_separator
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['1234.56', '1.234,56', ',', '.'])]
    public function testFromFormat (string $expected, string $value, string $decimal_separator = '.', string $thousands_separator = ','):void {

        self::assertSame(
            $expected,
            Decimal::fromFormat(
                $value,
                $decimal_separator,
                $thousands_separator
            )->value()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     * @param \FireHub\Core\Meta\Enum\Number\Format $format
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['1234.56', '1 234.56', Format::SI])]
    public function testFromStandard (string $expected, string $value, Format $format):void {

        self::assertSame($expected,  Decimal::fromStandard($value, $format)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param int $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     * @throws \FireHub\Foundation\Number\Exception\InvalidConversionException
     *
     * @return void
     */
    #[TestWith([10, '10.0'])]
    public function testToInteger (int $expected, string $value):void {

        self::assertSame($expected, new Decimal($value)->toInteger()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     *
     * @return void
     */
    #[TestWith(['10.1'])]
    public function testToIntegerInvalid (string $value):void {

        $this->expectException(InvalidConversionException::class);

        new Decimal($value)->toInteger();

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     *
     * @return void
     */
    #[TestWith([10.2, '10.2'])]
    public function testToReal (float $expected, string $value):void {

        self::assertSame($expected, new Decimal($value)->toReal()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param numeric-string $value
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     *
     * @return void
     */
    #[TestWith(['9.99', '9.99'])]
    public function testValue (string $expected, string $value):void {

        self::assertSame($expected, new Decimal($value)->value());

    }

}