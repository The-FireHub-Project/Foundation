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

namespace FireHub\Tests\Foundation\Unit\Str\Case;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Str\Case\Casing;
use FireHub\Foundation\Str;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Transforms characters between different letter cases
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Casing::class)]
final class CasingTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['firehub', 'FireHub'])]
    public function testLower (string $expected, string $value):void {

        self::assertSame($expected, new Casing(new Str($value))->lower()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['FIREHUB', 'FireHub'])]
    public function testUpper (string $expected, string $value):void {

        self::assertSame($expected, new Casing(new Str($value))->upper()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['tHE fIREhUB pROJECT', 'The FireHub Project'])]
    public function testSwap (string $expected, string $value):void {

        self::assertSame($expected, new Casing(new Str($value))->swap()->value());

    }

}