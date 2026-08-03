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
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\CaseMode;

/**
 * ### Tokenizes strings into structured segments
 *
 * Provides high-level string tokenization capabilities for splitting strings into meaningful segments such as words,
 * sentences, and character groups.
 *
 * This class serves as a shared foundation for string operations that require semantic string decomposition, including
 * case conversion, formatting, and text transformation features.
 * @since 1.0.0
 *
 * @todo Tokenizer::sentences(), Tokenizer::graphemes(), Tokenizer::characters();
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
     * ### Splits the string into words
     *
     * <code>
     * use FireHub\Foundation\Str\Tokenizer;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $string = new Tokenizer('FireHubProject', Encoding::UTF_8)->words();
     *
     * // ['fire', 'hub', 'project']
     *
     * $string = new Tokenizer('fireHubProject', Encoding::UTF_8)->words();
     *
     * // ['fire', 'hub', 'project']
     *
     * $string = new Tokenizer('fire_hub_project', Encoding::UTF_8)->words();
     *
     * // ['fire', 'hub', 'project']
     *
     * $string = new Tokenizer('fire-hub-project', Encoding::UTF_8)->words();
     *
     * // ['fire', 'hub', 'project']
     *
     * $string = new Tokenizer('ŠirokiBrijeg', Encoding::UTF_8)->words();
     *
     * // ['široki', 'brijeg']
     *
     * $string = new Tokenizer('ŽELJKO_ČIČAK', Encoding::UTF_8)->words();
     *
     * // ['željko', 'čičak']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Regex::replace() To split camelCase.
     * @uses \FireHub\Runtime\Str\MB\Regex::split() To split the string into words.
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the words to lowercase.
     * @uses \FireHub\Runtime\Arr\Transform::map() To convert the words to lowercase.
     * @uses \FireHub\Runtime\Arr\Transform::filter() To remove empty words.
     * @uses \FireHub\Runtime\Arr\Access::values() To get the words.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::LOWER To convert the words to lowercase.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return list<string> The list of words.
     */
    public function words ():array {

        $value = Runtime\Str\MB\Regex::replace( // Split camelCase
                '([[:lower:]])([[:upper:]])',
                '\1 \2',
                $this->string
            )
                |> (static fn($x) => Runtime\Str\MB\Regex::replace( // Split XMLParser -> XML Parse
                    '([[:upper:]]+)([[:upper:]][[:lower:]])',
                    '\1 \2',
                    $x))
                |> (static fn($x) => Runtime\Str\MB\Regex::replace( // Normalize separators
                    '[[:space:]_\-\.]+',
                    ' ',
                    $x))
                |> trim(...);

        if ($value === '') return [];

        $words = Runtime\Str\MB\Regex::split('\s+', $value);

        return Runtime\Arr\Transform::map(
            $words,
            fn(string $word): string => Runtime\Str\MB\Casing::convert(
                    $word,
                    CaseMode::LOWER,
                    $this->encoding
                )
            )
                |> (static fn($x) => Runtime\Arr\Transform::filter($x, static fn(string $word): bool => $word !== ''))
                |> Runtime\Arr\Access::values(...);

    }

}