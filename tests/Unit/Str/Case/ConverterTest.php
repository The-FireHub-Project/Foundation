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
use FireHub\Foundation\Str\Case\Converter;
use FireHub\Foundation\Str;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Converts strings between different casing formats
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Converter::class)]
final class ConverterTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['Firehub', 'firehub'])]
    public function testCapitalize (string $expected, string $value):void {

        self::assertSame($expected, new Converter(new Str($value))->capitalize()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['fireHub', 'FireHub'])]
    public function testUncapitalize (string $expected, string $value):void {

        self::assertSame($expected, new Converter(new Str($value))->uncapitalize()->value());

    }

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

        self::assertSame($expected, new Converter(new Str($value))->lower()->value());

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

        self::assertSame($expected, new Converter(new Str($value))->upper()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @return void
     */
    #[TestWith(['The Firehub', 'the fireHub'])]
    public function testTitle (string $expected, string $value):void {

        self::assertSame($expected, new Converter(new Str($value))->title()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith(['theFirehubProject', 'the firehub project'])]
    #[TestWith(['', ''])]
    public function testCamel (string $expected, string $value):void {

        self::assertSame($expected, new Converter(new Str($value))->camel()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith(['TheFirehubProject', 'the firehub project'])]
    #[TestWith(['', ''])]
    public function testPascal (string $expected, string $value):void {

        self::assertSame($expected, new Converter(new Str($value))->pascal()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith(['the_firehub_project', 'the firehub project'])]
    #[TestWith(['', ''])]
    public function testSnake (string $expected, string $value):void {

        self::assertSame($expected, new Converter(new Str($value))->snake()->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith(['the-firehub-project', 'the firehub project'])]
    #[TestWith(['', ''])]
    public function testKebab (string $expected, string $value):void {

        self::assertSame($expected, new Converter(new Str($value))->kebab()->value());

    }

}