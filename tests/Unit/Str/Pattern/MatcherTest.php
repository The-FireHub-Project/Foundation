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
    After, Before, Contains, EndsWith, Is, Occurrences, StartsWith
};
use FireHub\Foundation\Str\Exception\PatternOccurrencesNumberException;
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
#[CoversClass(Before::class)]
#[CoversClass(After::class)]
#[CoversClass(Occurrences::class)]
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
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project', 'the firehub project'])]
    #[TestWith([false, 'the firehub project', 'firehub'])]
    public function testIs (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->is($pattern)
        );

    }

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
    public function testContains (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->contains($pattern)
        );

    }

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
    #[TestWith([true, 'the firehub project', 'the'])]
    public function testStartsWith (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->startsWith($pattern)
        );

    }

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
    #[TestWith([true, 'the firehub project', 'project'])]
    public function testEndsWith (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->endsWith($pattern)
        );

    }

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
    #[TestWith([false, 'the firehub project', 'the'])]
    public function testBefore (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->before($pattern)
        );

    }

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
    #[TestWith([false, 'the firehub project', 'project'])]
    public function testAfter (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->after($pattern)
        );

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param int $occurrences
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\PatternOccurrencesNumberException
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project, the firehub project', 2, 'firehub'])]
    #[TestWith([false, 'the firehub project, the firehub project', 3, 'firehub'])]
    public function testExactly (bool $expected, string $string, int $occurrences, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->exactly($occurrences, $pattern)
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $string
     * @param int $occurrences
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['the firehub project, the firehub project', 0, 'firehub'])]
    public function testExactlyException (string $string, int $occurrences, string $pattern):void {

        $this->expectException(PatternOccurrencesNumberException::class);

        new Matcher(new Str($string), 0)->exactly($occurrences, $pattern);

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param int $occurrences
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\PatternOccurrencesNumberException
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project, the firehub project', 2, 'firehub'])]
    #[TestWith([false, 'the firehub project, the firehub project', 3, 'firehub'])]
    public function testAtLeast (bool $expected, string $string, int $occurrences, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->atLeast($occurrences, $pattern)
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $string
     * @param int $occurrences
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['the firehub project, the firehub project', 0, 'firehub'])]
    public function testAtLeastException (string $string, int $occurrences, string $pattern):void {

        $this->expectException(PatternOccurrencesNumberException::class);

        new Matcher(new Str($string), 0)->atLeast($occurrences, $pattern);

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param int $occurrences
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\PatternOccurrencesNumberException
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project, the firehub project', 2, 'firehub'])]
    #[TestWith([false, 'the firehub project, the firehub project', 1, 'firehub'])]
    public function testAtMost (bool $expected, string $string, int $occurrences, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->atMost($occurrences, $pattern)
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $string
     * @param int $occurrences
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['the firehub project, the firehub project', 0, 'firehub'])]
    public function testAtMostException (string $string, int $occurrences, string $pattern):void {

        $this->expectException(PatternOccurrencesNumberException::class);

        new Matcher(new Str($string), 0)->atMost($occurrences, $pattern);

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param int $minimal
     * @param int $maximal
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\PatternOccurrencesNumberException
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project, the firehub project', 1, 2, 'firehub'])]
    #[TestWith([false, 'the firehub project, the firehub project', 3, 4, 'firehub'])]
    public function testBetween (bool $expected, string $string, int $minimal, int $maximal, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->between($minimal, $maximal, $pattern)
        );

    }

    /**
     * @since 1.0.0
     *
     * @param string $string
     * @param int $minimal
     * @param int $maximal
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['the firehub project, the firehub project', 0, 2, 'firehub'])]
    public function testBetweenMinimalException (string $string, int $minimal, int $maximal, string $pattern):void {

        $this->expectException(PatternOccurrencesNumberException::class);

        new Matcher(new Str($string), 0)->between($minimal, $maximal, $pattern);

    }

    /**
     * @since 1.0.0
     *
     * @param string $string
     * @param int $minimal
     * @param int $maximal
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['the firehub project, the firehub project', 1, 0, 'firehub'])]
    public function testBetweenMaximalException (string $string, int $minimal, int $maximal, string $pattern):void {

        $this->expectException(PatternOccurrencesNumberException::class);

        new Matcher(new Str($string), 0)->between($minimal, $maximal, $pattern);

    }

}