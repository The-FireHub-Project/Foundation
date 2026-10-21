<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.4
 * @package Foundation
 */

namespace FireHub\Foundation\Str;

use FireHub\Core\Type\Str\Encoding;
use FireHub\Foundation\Str\Exception\ {
    InvalidEncodingException, InvalidPatternException
};
use FireHub\Runtime;

/**
 * ### Tokenizes strings into structured segments
 *
 * Provides high-level string tokenization capabilities for splitting strings into meaningful segments such as words,
 * sentences, paragraphs, lines, grapheme clusters, and Unicode characters.
 *
 * Tokenization is performed with Unicode-aware operations while preserving the original character encoding of
 * returned segments.
 *
 * Sentence segmentation uses punctuation-based heuristics and does not provide full natural-language boundary
 * detection.
 * @since 1.0.0
 */
final readonly class Tokenizer {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param string $string <p>
     * The string to extract words from.
     * </p>
     * @param Encoding $encoding <p>
     * The encoding of the string.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private string $string,
        private Encoding $encoding
    ) {}

    /**
     * ### Extracts words from the string
     *
     * Extracts Unicode words consisting of letters and combining marks, optionally containing internal apostrophes.
     *
     * Words must begin with a Unicode letter. Punctuation, numbers, whitespace, and symbols are excluded from the
     * extracted results.
     *
     * Word boundaries are determined to use Unicode character properties without performing language-specific
     * segmentation.
     *
     * <code>
     * use FireHub\Foundation\Str\Tokenizer;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $string = new Tokenizer('FireHubProject', Encoding::UTF_8)->words();
     *
     * // ['FireHubProject']
     *
     * $string = new Tokenizer('fire_hub_project', Encoding::UTF_8)->words();
     *
     * // ['fire', 'hub', 'project']
     *
     * $string = new Tokenizer('fire-hub-project', Encoding::UTF_8)->words();
     *
     * // ['fire', 'hub', 'project']
     *
     * $string = new Tokenizer('firehub project', Encoding::UTF_8)->words();
     *
     * // ['firehub', 'project']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Tokenizer::convertToUtf8() To convert the string to UTF-8.
     * @uses \FireHub\Foundation\Str\Tokenizer::restoreEncoding() To restore the original string encoding.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To extract words.
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException If the conversion fails.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException If the string is not valid for the current
     * encoding.
     * @throws \FireHub\Foundation\Str\Exception\InvalidPatternException If an error occurred while performing a
     * regular expression search and replace type.
     *
     * @return array<int, string> The list of words.
     */
    public function words ():array {

        $value = $this->convertToUtf8();

        if ($value === '') return [];

        $result = Runtime\Str\SB\Regex::match(
            '/\p{L}[\p{L}\p{M}]*(?:[\'’]\p{L}[\p{L}\p{M}]*)*/u',
            $value,
            all: true,
            result: $matches
        );

        if ($result === false)
            throw new InvalidPatternException;

        /** @var array<array<int, string>> $matches */
        return $this->restoreEncoding($matches[0]); // @phpstan-ignore offsetAccess.notFound

    }

    /**
     * ### Splits the string into sentences
     *
     * Extracts sentences using common sentence-ending punctuation, including periods, question marks, exclamation
     * marks, and selected Unicode sentence terminators.
     *
     * Trailing quotation marks and closing brackets are preserved.
     *
     * Sentence boundaries are recognized when terminal punctuation is followed by whitespace or the end of the string.
     *
     * <code>
     * use FireHub\Foundation\Str\Tokenizer;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $tokenizer = new Tokenizer('Hello World! How are you? I am fine.', Encoding::UTF_8);
     *
     * $sentences = $tokenizer->sentences();
     *
     * // ['Hello World!', 'How are you?', 'I am fine.']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Tokenizer::convertToUtf8() To convert the string to UTF-8.
     * @uses \FireHub\Foundation\Str\Tokenizer::restoreEncoding() To restore the original string encoding.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To extract sentences.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the extracted sentence.
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException If the conversion fails.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException If the string is not valid for the current
     * encoding.
     * @throws \FireHub\Foundation\Str\Exception\InvalidPatternException If an error occurred while performing a
     * regular expression search and replace type.
     *
     * @return array<int, string> Extracted sentences.
     */
    public function sentences ():array {

        $value = $this->convertToUtf8();

        if ($value === '') return [];

        $result = Runtime\Str\SB\Regex::match(
            '~\S[\s\S]*?(?:[.!?。！？]+(?:["\'”’»)\]]*)(?=\s+|\z)|\z)~u',
            $value,
            all: true,
            result: $matches
        );

        if ($result === false)
            throw new InvalidPatternException;

        $sentences = [];

        foreach ($matches[0] as $sentence) { // @phpstan-ignore-line

            $sentence = Runtime\Str\SB\Transform::trim($sentence);

            if ($sentence !== '')
                $sentences[] = $sentence;

        }

        return $this->restoreEncoding($sentences);

    }

    /**
     * ### Splits the string into paragraphs
     *
     * Splits text into paragraphs separated by one or more blank lines.
     *
     * Supports different newline conventions, including LF, CRLF, CR, and Unicode line separators.
     *
     * Leading and trailing whitespace is removed from each paragraph, while internal line breaks are preserved.
     *
     * <code>
     * use FireHub\Foundation\Str\Tokenizer;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $tokenizer = new Tokenizer("First paragraph.\n\nSecond paragraph.", Encoding::UTF_8);
     *
     * $paragraphs = $tokenizer->paragraphs();
     *
     * // ['First paragraph.', 'Second paragraph.']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Tokenizer::convertToUtf8() To convert the string to UTF-8.
     * @uses \FireHub\Foundation\Str\Tokenizer::restoreEncoding() To restore the original string encoding.
     * @uses \FireHub\Runtime\Str\SB\Regex::split() To split the string into paragraphs.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the extracted sentence.
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException If the conversion fails.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If error while performing a regular expression split.
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException If the string is not valid for the current
     * encoding.
     *
     * @return array<int, string> Extracted paragraphs.
     */
    public function paragraphs ():array {

        $value = $this->convertToUtf8();

        if ($value === '') return [];

        $paragraphs = Runtime\Str\SB\Regex::split(
            '/\R(?:\h*\R)+/u',
            $value,
            -1,
            true
        );

        $result = [];

        foreach ($paragraphs as $paragraph) {

            $paragraph = Runtime\Str\SB\Transform::trim($paragraph);

            if ($paragraph !== '')
                $result[] = $paragraph;

        }

        return $this->restoreEncoding($result);

    }

    /**
     * ### Splits the string into lines
     *
     * Splits text into individual lines using Unicode-aware newline detection.
     *
     * Supports LF, CRLF, CR, and Unicode line separators.
     *
     * Empty lines are preserved, including a trailing empty line when the input ends with a line separator.
     *
     * <code>
     * use FireHub\Foundation\Str\Tokenizer;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $tokenizer = new Tokenizer("First line\nSecond line", Encoding::UTF_8);
     *
     * $lines = $tokenizer->lines();
     *
     * // ['First line', 'Second line']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Tokenizer::convertToUtf8() To convert the string to UTF-8.
     * @uses \FireHub\Foundation\Str\Tokenizer::restoreEncoding() To restore the original string encoding.
     * @uses \FireHub\Runtime\Str\SB\Regex::split() To split the string into lines.
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException If the conversion fails.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If error while performing a regular expression split.
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException If the string is not valid for the current
     * encoding.
     *
     * @return array<int, string> Extracted lines.
     */
    public function lines ():array {

        $value = $this->convertToUtf8();

        if ($value === '') return [];

        $lines = Runtime\Str\SB\Regex::split('/\R/u', $value);

        return $this->restoreEncoding($lines);

    }

    /**
     * ### Splits the string into Unicode grapheme clusters
     *
     * Splits the string into extended grapheme clusters using Unicode grapheme boundary rules supported by PCRE.
     *
     * A grapheme cluster may contain multiple Unicode code points that visually represent a single character.
     *
     * <code>
     * use FireHub\Foundation\Str\Tokenizer;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $tokenizer = new Tokenizer("e\u{0301}👍", Encoding::UTF_8);
     *
     * $graphemes = $tokenizer->graphemes();
     *
     * // ["e\u{0301}", '👍']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Tokenizer::convertToUtf8() To convert the string to UTF-8.
     * @uses \FireHub\Foundation\Str\Tokenizer::restoreEncoding() To restore the original string encoding.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To extract grapheme clusters.
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException If the conversion fails.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException If the string is not valid for the current
     * encoding.
     * @throws \FireHub\Foundation\Str\Exception\InvalidPatternException If an error occurred while performing a
     * regular expression search and replace type.
     *
     * @return array<int, string> Unicode grapheme clusters.
     */
    public function graphemes ():array {

        $value = $this->convertToUtf8();

        if ($value === '') return [];

        $result = Runtime\Str\SB\Regex::match(
            '/\X/u',
            $value,
            all: true,
            result: $matches
        );

        if ($result === false)
            throw new InvalidPatternException;

        return $this->restoreEncoding($matches[0]); // @phpstan-ignore-line

    }

    /**
     * ### Splits the string into Unicode characters
     *
     * Splits the input string into individual Unicode code points according to the specified encoding.
     *
     * Unlike grapheme segmentation, combining marks are returned as separate characters.
     *
     * <code>
     * use FireHub\Foundation\Str\Tokenizer;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $tokenizer = new Tokenizer('FireHub', Encoding::UTF_8);
     *
     * $characters = $tokenizer->characters();
     *
     * // ['F', 'i', 'r', 'e', 'H', 'u', 'b']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Inspection::checkEncoding() To validate the string encoding.
     * @uses \FireHub\Runtime\Str\MB\Access::split() To split the string into characters.
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException If the $length parameter is less than 1.
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException If the string is not valid for the current
     * encoding.
     *
     * @return array<int, string> Individual Unicode characters.
     */
    public function characters ():array {

        if (!Runtime\Str\MB\Inspection::checkEncoding($this->string, $this->encoding))
            throw new InvalidEncodingException;

        if ($this->string === '') return [];

        return Runtime\Str\MB\Access::split(
            $this->string,
            1,
            $this->encoding
        );

    }

    /**
     * ### Converts the input string to UTF-8
     *
     * Validates the original encoding before conversion.
     *
     * Unicode regular expression operations are performed on UTF-8 encoded strings to ensure consistent behavior.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Inspection::checkEncoding() To validate the string encoding.
     * @uses \FireHub\Runtime\Str\MB\Transform::convertEncoding() To convert the string to UTF-8.
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException If the conversion fails.
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException If the string is not valid for the current
     * encoding.
     *
     * @return string UTF-8 encoded string.
     */
    private function convertToUtf8 ():string {

        if (!Runtime\Str\MB\Inspection::checkEncoding($this->string, $this->encoding))
            throw new InvalidEncodingException;

        if ($this->encoding === Encoding::UTF_8)
            return $this->string;

        return Runtime\Str\MB\Transform::convertEncoding(
            $this->string,
            Encoding::UTF_8,
            $this->encoding
        );

    }

    /**
     * ### Restores the original string encoding
     *
     * Converts UTF-8 segments back to the original encoding used when constructing the tokenizer.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Transform::convertEncoding() To convert the string to the original encoding.
     *
     * @param array<int, string> $segments <p>
     * UTF-8 encoded string segments.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\EncodingConversionFailedException If the conversion fails.
     *
     * @return array<int, string> Segments using the original encoding.
     */
    private function restoreEncoding (array $segments):array {

        if ($this->encoding === Encoding::UTF_8)
            return $segments;

        $result = [];

        foreach ($segments as $segment) {

            $result[] = Runtime\Str\MB\Transform::convertEncoding(
                $segment,
                $this->encoding,
                Encoding::UTF_8
            );

        }

        return $result;

    }

}