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

namespace FireHub\Tests\Foundation\Unit\Str\Pattern;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Str\Pattern\Matcher;
use FireHub\Foundation\Str;
use FireHub\Foundation\Str\Pattern\Expression\ {
    Contains, EndsWith, Is, StartsWith
};
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Matches strings against regular expressions
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Matcher::class)]
#[CoversClass(Is::class)]
#[CoversClass(Contains::class)]
#[CoversClass(StartsWith::class)]
#[CoversClass(EndsWith::class)]
final class MatcherTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project', 'firehub'])]
    public function testCustom (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->custom($pattern)
        );

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $pattern
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project', 'the firehub project'])]
    #[TestWith([false, 'the firehub project', 'firehub'])]
    public function testIs (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->is()->custom($pattern)
        );

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $pattern
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project', 'firehub'])]
    public function testContains (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->contains()->custom($pattern)
        );

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->contains()->ascii()
        );

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $pattern
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project', 'the'])]
    public function testStartsWith (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->startsWith()->custom($pattern)
        );

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $pattern
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project', 'project'])]
    public function testEndsWith (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->endsWith()->custom($pattern)
        );

    }

}