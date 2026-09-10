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

namespace FireHub\Tests\Foundation\Unit\Number\Operation;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Number\Operation\NumericFormat;
use FireHub\Foundation\Number\Exception\InvalidFractionalException;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test formatting and parsing operations for numeric values
 * @since 1.0.0
 */
#[Small]
#[Group('number')]
#[CoversClass(NumericFormat::class)]
final class NumericFormatTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param numeric-string $expected
     * @param string $value
     * @param string $decimal_separator
     * @param string $thousands_separator
     *
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['1234.56', '1.234,56', ',', '.'])]
    public function testToString (string $expected, string $value, string $decimal_separator = '.', string $thousands_separator = ','):void {

        self::assertSame(
            $expected,
            new NumericFormat(
                $value,
                $decimal_separator,
                $thousands_separator
            )->toString()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $value
     * @param string $decimal_separator
     * @param string $thousands_separator
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['9.99', '.', '.'])]
    #[TestWith(['9.99', '_', ','])]
    public function testFromFormatInvalid (string $value, string $decimal_separator = '.', string $thousands_separator = ','):void {

        $this->expectException(InvalidFractionalException::class);

        new NumericFormat(
            $value,
            $decimal_separator,
            $thousands_separator
        )->toString();

    }

}