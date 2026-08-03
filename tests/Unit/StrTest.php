<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Foundation\Tests
 */

namespace FireHub\Tests\Foundation\Unit;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Str;
use FireHub\Core\Type\Str\Encoding;
use FireHub\Runtime;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable string value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Str::class)]
final class StrTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param null|scalar|Stringable $value
     *
     * @return void
     */
    #[TestWith(['', null])]
    #[TestWith(['FireHub', 'FireHub'])]
    #[TestWith(['10', 10])]
    #[TestWith(['2.34', 2.34])]
    #[TestWith(['', false])]
    #[TestWith(['1', true])]
    public function testOf (string $expected, null|string|int|float|bool|Stringable $value):void {

        self::assertSame($expected, Str::of($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param iterable<mixed, string> $values
     * @param string $separator [optional]
     * @param null|string $conjunction [optional]
     *
     * @return void
     */
    #[TestWith(['Fire and Hub', ['Fire', 'Hub'], ' ', 'and'])]
    #[TestWith(['a-b-c', ['a', 'b', 'c'], '-'])]
    public function testJoin (string $expected, iterable $values, string $separator = '', ?string $conjunction = null):void {

        self::assertSame($expected, Str::join($values, $separator, $conjunction)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['FireHub', 'FireHub'])]
    public function testValue (string $expected, string $value):void {

        self::assertSame($expected, new Str($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $value
     * @param \FireHub\Core\Type\Str\Encoding $encoding
     *
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith(['FireHub', Encoding::ASCII])]
    public function testEncoding (string $value, Encoding $encoding):void {

        self::assertSame(Runtime\Str\MB\Configuration::encoding(), new Str($value)->encoding());

        self::assertSame($encoding, new Str($value)->withEncoding($encoding)->encoding());

    }

    /**
     * @since 1.0.0
     *
     * @param float $expected
     * @param string $value
     *
     * @return void
     */
    #[TestWith([123, '123'])]
    public function testConvert (float $expected, string $value):void {

        self::assertSame($expected, new Str($value)->convert()->safe()->float());

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
    #[TestWith([true, 'FireHub', 'F'])]
    #[TestWith([true, 'FireHub', ''])]
    #[TestWith([false, 'FireHub', 'f'])]
    public function testStartsWith (bool $expected, string $string, string $value):void {

        self::assertSame($expected, new Str($string)->startsWith($value));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param iterable<string> $values
     *
     * @return void
     */
    #[TestWith([true, 'FireHub', ['D', 'F', 'H']])]
    #[TestWith([false, 'FireHub', ['D', 'G', 'H']])]
    public function testStartsWithAny (bool $expected, string $string, iterable $values):void {

        self::assertSame($expected, new Str($string)->startsWithAny($values));

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
    #[TestWith([true, 'FireHub', 'b'])]
    #[TestWith([true, 'FireHub', ''])]
    #[TestWith([false, 'FireHub', 'B'])]
    public function testEndsWith (bool $expected, string $string, string $value):void {

        self::assertSame($expected, new Str($string)->endsWith($value));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param iterable<string> $values
     *
     * @return void
     */
    #[TestWith([true, 'FireHub', ['v', 'b', 'n']])]
    #[TestWith([false, 'FireHub', ['v', 'm', 'n']])]
    public function testEndsWithAny (bool $expected, string $string, iterable $values):void {

        self::assertSame($expected, new Str($string)->endsWithAny($values));

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
    #[TestWith([true, 'FireHub', 'ire'])]
    #[TestWith([false, 'FireHub', 'test'])]
    public function testContains (bool $expected, string $string, string $value):void {

        self::assertSame($expected, new Str($string)->contains($value));

    }

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param iterable<string> $values
     *
     * @return void
     */
    #[TestWith([true, 'FireHub', ['i', 'r', 'e']])]
    #[TestWith([false, 'FireHub', ['i', 'r', 'e', 's']])]
    public function testContainsAll (bool $expected, string $string, iterable $values):void {

        self::assertSame($expected, new Str($string)->containsAll($values));

    }

}