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

namespace FireHub\Tests\Foundation\Unit\Str;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Str\Caseless;
use FireHub\Foundation\Str\Pattern;
use FireHub\Foundation\Str\Operation\Extract;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test case-insensitive string value object
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Caseless::class)]
final class CaselessTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testExtract ():void {

        self::assertInstanceOf(Extract::class, new Caseless('fireHub')->extract());

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testPattern ():void {

        self::assertInstanceOf(Pattern::class, new Caseless('fireHub')->pattern());

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $value
     *
     * @return void
     */
    #[TestWith([true, 'FireHub', 'f'])]
    public function testStartsWith (bool $expected, string $string, string $value):void {

        self::assertSame($expected, new Caseless($string)->startsWith($value));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $value
     *
     * @return void
     */
    #[TestWith([true, 'FireHub', 'B'])]
    public function testEndsWith (bool $expected, string $string, string $value):void {

        self::assertSame($expected, new Caseless($string)->endsWith($value));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $value
     *
     * @return void
     */
    #[TestWith([true, 'FireHub', 'reh'])]
    public function testContains (bool $expected, string $string, string $value):void {

        self::assertSame($expected, new Caseless($string)->contains($value));

    }

    /**
     * @since 1.0.0
     *
     * @param false|non-negative-int $expected
     * @param string $string
     * @param string $find
     *
     * @return void
     */
    #[TestWith([4, 'The FireHub Project', 'f'])]
    #[TestWith([false, 'The FireHub Project', 'x'])]
    public function testIndexOf (int|false $expected, string $string, string $find):void {

        self::assertSame($expected, new Caseless($string)->indexOf($find));

    }

    /**
     * @since 1.0.0
     *
     * @param false|non-negative-int $expected
     * @param string $string
     * @param string $find
     *
     * @return void
     */
    #[TestWith([18, 'The FireHub Project', 'T'])]
    #[TestWith([false, 'The FireHub Project', 'x'])]
    public function testLastIndexOf (int|false $expected, string $string, string $find):void {

        self::assertSame($expected, new Caseless($string)->lastIndexOf($find));

    }

}