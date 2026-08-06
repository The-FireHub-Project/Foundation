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
use FireHub\Core\Type\Str\Encoding;
use FireHub\Core\Meta\Enum\Side;
use FireHub\Core\Foundation\Constant\Numeric\IntegerLimits;
use FireHub\Foundation\Str;
use FireHub\Foundation\Str\Case\ {
    Casing, Converter
};
use FireHub\Foundation\Str\Pattern;
use FireHub\Foundation\Str\Operation\Extract;
use FireHub\Runtime;
use FireHub\Runtime\Exception\ {
    EmptySeparatorException, StringSplitLengthException
};
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
     * @return void
     */
    public function testCase ():void {

        self::assertInstanceOf(Casing::class, new Str('fireHub')->case());

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testTransform ():void {

        self::assertInstanceOf(Converter::class, new Str('fireHub')->transform());

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testPattern ():void {

        self::assertInstanceOf(Pattern::class, new Str('fireHub')->pattern());

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
     * @return void
     */
    public function testExtract ():void {

        self::assertInstanceOf(Extract::class, new Str('fireHub')->extract());

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

    /**
     * @since 1.0.0
     *
     * @param non-negative-int $expected
     * @param string $string
     *
     * @return void
     */
    #[TestWith([19, 'The FireHub Project'])]
    public function testLength (int $expected, string $string):void {

        self::assertSame($expected, new Str($string)->length());

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
    #[TestWith([4, 'The FireHub Project', 'F'])]
    #[TestWith([false, 'The FireHub Project', 'x'])]
    public function testIndexOf (int|false $expected, string $string, string $find):void {

        self::assertSame($expected, new Str($string)->indexOf($find));

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
    #[TestWith([18, 'The FireHub Project', 't'])]
    #[TestWith([false, 'The FireHub Project', 'x'])]
    public function testLastIndexOf (int|false $expected, string $string, string $find):void {

        self::assertSame($expected, new Str($string)->lastIndexOf($find));

    }

    /**
     * @since 1.0.0
     *
     * @param list<non-empty-string> $expected
     * @param string $string
     * @param positive-int $length
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException
     *
     * @return void
     */
    #[TestWith([['The F', 'ireHu', 'b Pro', 'ject'], 'The FireHub Project', 5])]
    public function testSplit (array $expected, string $string, int $length = 1):void {

        self::assertSame($expected, new Str($string)->split($length));

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testSplitNonPositiveLength ():void {

        $this->expectException(StringSplitLengthException::class);

        new Str('')->split(0);

    }

    /**
     * @since 1.0.0
     *
     * @param list<non-empty-string> $expected
     * @param string $string
     * @param non-empty-string $separator
     * @param int<min, max> $limit
     *
     * @throws \FireHub\Runtime\Exception\EmptySeparatorException
     *
     * @return void
     */
    #[TestWith([['The', 'FireHub', 'Project'], 'The FireHub Project', ' '])]
    public function testExplode (array $expected, string $string, string $separator, int $limit = IntegerLimits::MAX):void {

        self::assertSame($expected, new Str($string)->explode($separator, $limit));

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testExplodeEmptySeparator ():void {

        $this->expectException(EmptySeparatorException::class);

        new Str('')->explode('');

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['The FireHub', 'FireHub', 'The '])]
    public function testPrepend (string $expected, string $string, string $value):void {

        self::assertSame($expected, new Str($string)->prepend($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['FireHub Project', 'FireHub', ' Project'])]
    public function testAppend (string $expected, string $string, string $value):void {

        self::assertSame($expected, new Str($string)->append($value)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $prefix
     *
     * @return void
     */
    #[TestWith(['The FireHub', 'FireHub', 'The '])]
    #[TestWith(['The FireHub', 'The FireHub', 'The '])]
    public function testEnsurePrefix (string $expected, string $string, string $prefix):void {

        self::assertSame($expected, new Str($string)->ensurePrefix($prefix)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $suffix
     *
     * @return void
     */
    #[TestWith(['FireHub Project', 'FireHub', ' Project'])]
    #[TestWith(['FireHub Project', 'FireHub Project', ' Project'])]
    public function testEnsureSuffix (string $expected, string $string, string $suffix):void {

        self::assertSame($expected, new Str($string)->ensureSuffix($suffix)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $with
     *
     * @return void
     */
    #[TestWith(['*FireHub*', 'FireHub', '*'])]
    public function testSurround (string $expected, string $string, string $with):void {

        self::assertSame($expected, new Str($string)->surround($with)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith(['" The FireHub Project... "', '“  The   FireHub Project…  ”'])]
    public function testTidy (string $expected, string $string):void {

        self::assertSame($expected, new Str($string)->tidy()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $value
     * @param int $position
     *
     * @return void
     */
    #[TestWith(['The -FireHub Project', 'The FireHub Project', '-', 4])]
    #[TestWith(['The FireHub Pro-ject', 'The FireHub Project', '-', -4])]
    #[TestWith(['The FireHub Project-', 'The FireHub Project', '-', 100])]
    #[TestWith(['-The FireHub Project', 'The FireHub Project', '-', -100])]
    public function testInsert (string $expected, string $string, string $value, int $position):void {

        self::assertSame($expected, new Str($string)->insert($value, $position)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param int $from
     * @param int $length
     * @param int $to
     *
     * @return void
     */
    #[TestWith(['FHuireb', 'FireHub', 4, 2, 1])]
    #[TestWith(['FirHueb', 'FireHub', 4, 2, -2])]
    public function testMove (string $expected, string $string, int $from, int $length, int $to):void {

        self::assertSame($expected, new Str($string)->move($from, $length, $to)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param int $from
     * @param int $until
     * @param string $with
     *
     * @return void
     */
    #[TestWith(['The Awesome Project', 'The FireHub Project', 4, 11, 'Awesome'])]
    #[TestWith(['The Awesome Project', 'The FireHub Project', -15, -8, 'Awesome'])]
    public function testOverwrite (string $expected, string $string, int $from, int $until, string $with):void {

        self::assertSame($expected, new Str($string)->overwrite($from, $until, $with)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param int $times
     * @param string $separator
     *
     * @return void
     */
    #[TestWith(['FireHub-FireHub-FireHub', 'FireHub', 3, '-'])]
    public function testRepeat (string $expected, string $string, int $times, string $separator = ''):void {

        self::assertSame($expected, new Str($string)->repeat($times, $separator)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param int $times
     * @param string $separator
     *
     * @return void
     */
    #[TestWith(['FireHub-FireHub-FireHub-FireHub', 'FireHub', 3, '-'])]
    public function testDuplicate (string $expected, string $string, int $times, string $separator = ''):void {

        self::assertSame($expected, new Str($string)->duplicate($times, $separator)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException
     *
     * @return void
     */
    #[TestWith(['tcejorP buHeriF ehT', 'The FireHub Project'])]
    public function testReverse (string $expected, string $string):void {

        self::assertSame($expected, new Str($string)->reverse()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param int $length
     * @param non-empty-string $pad
     * @param \FireHub\Core\Meta\Enum\Side $side
     *
     * @throws \FireHub\Runtime\Exception\EmptyPadException
     *
     * @return void
     */
    #[TestWith(['The FireHub Project-----------', 'The FireHub Project', 30, '-'])]
    #[TestWith(['-----The FireHub Project------', 'The FireHub Project', 30, '-', Side::BOTH])]
    public function testPad (string $expected, string $string, int $length, string $pad = ' ', Side $side = Side::RIGHT):void {

        self::assertSame($expected, new Str($string)->pad($length, $pad, $side)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param \FireHub\Core\Meta\Enum\Side $side
     * @param null|string $characters
     *
     * @return void
     */
    #[TestWith(['FireHub', '   FireHub   '])]
    public function testTrim (string $expected, string $string, Side $side = Side::BOTH, ?string $characters = null):void {

        self::assertSame($expected, new Str($string)->trim($side, $characters)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param int $length
     * @param string $with
     *
     * @return void
     */
    #[TestWith(['The Fir...', 'The FireHub Project', 10])]
    public function testTruncate (string $expected, string $string, int $length, string $with = '...'):void {

        self::assertSame($expected, new Str($string)->truncate($length, $with)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param int $length
     * @param string $with
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith(['The...', 'The FireHub Project', 13])]
    #[TestWith(['The FireHub...', 'The FireHub Project', 14])]
    public function testTruncateSafe (string $expected, string $string, int $length, string $with = '...'):void {

        self::assertSame($expected, new Str($string)->truncateSafe($length, $with)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $string
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException
     *
     * @return void
     */
    #[TestWith(['The FireHub Project'])]
    public function testShuffle (string $string):void {

        $expected = $string;

        new Str($string)->shuffle();

        self::assertEqualsCanonicalizing($expected, $string);

    }

}