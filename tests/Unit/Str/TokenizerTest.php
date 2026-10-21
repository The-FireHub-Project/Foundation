<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Foundation\Tests
 */

namespace FireHub\Tests\Foundation\Unit\Str;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Str\Tokenizer;
use FireHub\Core\Type\Str\Encoding;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Tokenizes strings into structured segments
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Tokenizer::class)]
final class TokenizerTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param list<string> $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException
     * @throws \FireHub\Foundation\Str\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([['FireHubProject'], 'FireHubProject'])]
    #[TestWith([['fire', 'hub', 'project'], 'fire_hub_project'])]
    #[TestWith([['fire', 'hub', 'project'], 'fire-hub-project'])]
    #[TestWith([['firehub', 'project'], 'firehub project'])]
    public function testWords (array $expected, string $value):void {

        self::assertSame($expected, new Tokenizer($value, Encoding::UTF_8)->words());

    }

    /**
     * @since 1.0.0
     *
     * @param list<string> $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException
     * @throws \FireHub\Foundation\Str\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([['Hello World!', 'How are you?', 'I am fine.'], 'Hello World! How are you? I am fine.'])]
    public function testSentences (array $expected, string $value):void {

        self::assertSame($expected, new Tokenizer($value, Encoding::UTF_8)->sentences());

    }

    /**
     * @since 1.0.0
     *
     * @param list<string> $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith([['First paragraph.', 'Second paragraph.'], "First paragraph.\n\nSecond paragraph."])]
    public function testParagraphs (array $expected, string $value):void {

        self::assertSame($expected, new Tokenizer($value, Encoding::UTF_8)->paragraphs());

    }

    /**
     * @since 1.0.0
     *
     * @param list<string> $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith([['First line', 'Second line'], "First line\nSecond line"])]
    public function testLines (array $expected, string $value):void {

        self::assertSame($expected, new Tokenizer($value, Encoding::UTF_8)->lines());

    }

    /**
     * @since 1.0.0
     *
     * @param list<string> $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException
     * encoding.
     * @throws \FireHub\Foundation\Str\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([["e\u{0301}", '👍'], "e\u{0301}👍"])]
    public function testGraphemes (array $expected, string $value):void {

        self::assertSame($expected, new Tokenizer($value, Encoding::UTF_8)->graphemes());

    }

    /**
     * @since 1.0.0
     *
     * @param list<string> $expected
     * @param string $value
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException
     *
     * @return void
     */
    #[TestWith([['F', 'i', 'r', 'e', 'H', 'u', 'b'], 'FireHub',])]
    public function testCharacters (array $expected, string $value):void {

        self::assertSame($expected, new Tokenizer($value, Encoding::UTF_8)->characters());

    }

}