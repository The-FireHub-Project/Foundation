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
use FireHub\Foundation\Str;
use FireHub\Foundation\Str\Operation\Extract;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Extracts portions of strings using delimiters and boundaries
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Extract::class)]
final class ExtractTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param int $from
     * @param null|int $length
     *
     * @return void
     */
    #[TestWith(['e FireHub Project', 'The FireHub Project', 2])]
    #[TestWith(['Fi', 'The FireHub Project', 4, 2])]
    #[TestWith(['ojec', 'The FireHub Project', -5, -1])]
    public function testSlice (string $expected, string $string, int $from, ?int $length = null):void {

        self::assertSame($expected, new Str($string)->extract()->slice($from, $length)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $find
     *
     * @return void
     */
    #[TestWith(['FireHub Project', 'The FireHub Project', 'Fi'])]
    #[TestWith(['', 'The FireHub Project', 'test'])]
    public function testFrom (string $expected, string $string, string $find):void {

        self::assertSame($expected, new Str($string)->extract()->from($find)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $find
     *
     * @return void
     */
    #[TestWith(['FireHub Project', 'The FireHub Project, The FireHub Project', 'FireHub'])]
    #[TestWith(['', 'The FireHub Project, The FireHub Project', 'test'])]
    public function testFromLast (string $expected, string $string, string $find):void {

        self::assertSame($expected, new Str($string)->extract()->fromLast($find)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $find
     *
     * @return void
     */
    #[TestWith(['The ', 'The FireHub Project', 'Fi'])]
    #[TestWith(['', 'The FireHub Project', 'test'])]
    public function testUntil (string $expected, string $string, string $find):void {

        self::assertSame($expected, new Str($string)->extract()->until($find)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $find
     *
     * @return void
     */
    #[TestWith(['The FireHub Project, The ', 'The FireHub Project, The FireHub Project', 'Fi'])]
    #[TestWith(['', 'The FireHub Project, The FireHub Project', 'test'])]
    public function testUntilLast (string $expected, string $string, string $find):void {

        self::assertSame($expected, new Str($string)->extract()->untilLast($find)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $find
     *
     * @return void
     */
    #[TestWith(['FireHub Project', 'The FireHub Project', 'The '])]
    public function testAfter (string $expected, string $string, string $find):void {

        self::assertSame($expected, new Str($string)->extract()->after($find)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $find
     *
     * @return void
     */
    #[TestWith(['FireHub Project', 'The FireHub Project, The FireHub Project', 'The '])]
    public function testAfterLast (string $expected, string $string, string $find):void {

        self::assertSame($expected, new Str($string)->extract()->afterLast($find)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $start
     * @param string $end
     *
     * @return void
     */
    #[TestWith(['FireHub] Project, The FireHub [Project', 'The [FireHub] Project, The FireHub [Project]', '[', ']'])]
    #[TestWith(['', 'The FireHub Project, The FireHub Project', '[', ']'])]
    public function testBetween (string $expected, string $string, string $start, string $end):void {

        self::assertSame($expected, new Str($string)->extract()->between($start, $end)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $start
     * @param string $end
     *
     * @return void
     */
    #[TestWith(['FireHub', 'The [FireHub] Project, The FireHub [Project]', '[', ']'])]
    #[TestWith(['', 'The FireHub Project, The FireHub Project', '[', ']'])]
    public function testBetweenFirst (string $expected, string $string, string $start, string $end):void {

        self::assertSame($expected, new Str($string)->extract()->betweenFirst($start, $end)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $start
     * @param string $end
     *
     * @return void
     */
    #[TestWith(['Project', 'The [FireHub] Project, The FireHub [Project]', '[', ']'])]
    #[TestWith(['', 'The FireHub Project, The FireHub Project', '[', ']'])]
    public function testBetweenLast (string $expected, string $string, string $start, string $end):void {

        self::assertSame($expected, new Str($string)->extract()->betweenLast($start, $end)->value());

    }

}