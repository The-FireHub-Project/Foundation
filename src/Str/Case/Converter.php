<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Foundation
 */

namespace FireHub\Foundation\Str\Case;

use FireHub\Core\Type\Str;
use FireHub\Foundation\Str\Boundary\CaseTransformable;
use FireHub\Foundation\Str\Tokenizer;
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\CaseMode;

/**
 * ### Converts strings between different casing formats
 *
 * This class converts strings into common naming conventions such as camelCase, PascalCase, snake_case, kebab-case,
 * and sentence case.
 * @since 1.0.0
 *
 * @template TCaseTransformable of \FireHub\Foundation\Str\Boundary\CaseTransformable
 *
 * @extends \FireHub\Foundation\Str\Case\Casing<TCaseTransformable>
 */
readonly class Converter extends Casing {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Str<string>&TCaseTransformable $str <p>
     * The string to convert.
     * </p>
     *
     * @return void
     */
    public function __construct (Str&CaseTransformable $str) {

        parent::__construct($str);

    }

    /**
     * ### Capitalizes the first character of the string
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('firehub'))->capitalize();
     *
     * // 'Firehub'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::capitalize() To capitalize the first character of the string.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @return TCaseTransformable Returns a new instance with the first character capitalized.
     */
    public function capitalize ():CaseTransformable {

        return new $this->str(
            Runtime\Str\MB\Casing::capitalize($this->str->value()),
            $this->str->encoding()
        );

    }

    /**
     * ### Uncapitalizes the first character of the string
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('FireHub'))->capitalize();
     *
     * // 'fireHub'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::uncapitalize() To Uncapitalize the first character of the string.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @return TCaseTransformable Returns a new instance with the first character uncapitalized.
     */
    public function uncapitalize ():CaseTransformable {

        return new $this->str(
            Runtime\Str\MB\Casing::uncapitalize($this->str->value()),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to the title case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the fireHub'))->title();
     *
     * // 'The Firehub'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the string the title case.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::UPPER To convert the string the title case.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted the title case.
     */
    public function title ():CaseTransformable {

        return new $this->str(
            Runtime\Str\MB\Casing::convert(
                $this->str->value(),
                CaseMode::TITLE,
                $this->str->encoding()
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to a train case
     *
     * Converts the string into train case format where each word is capitalized.
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the FireHub project'))->train();
     *
     * // 'The FireHub Project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To get the words of the string.
     * @uses \FireHub\Runtime\Arr\Transform::map() To transform words.
     * @uses \FireHub\Runtime\Str\MB\Casing::capitalize() To capitalize words.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join words.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return \FireHub\Foundation\Str\Boundary\CaseTransformable Returns a new instance with the string converted
     * to train case.
     */
    public function train ():CaseTransformable {

        return new $this->str(
            Runtime\Str\SB\Delimiter::implode(
                Runtime\Arr\Transform::map(
                    $this->words(),
                    static fn(string $word):string => Runtime\Str\MB\Casing::capitalize($word)
                ),
                ' '
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to camel case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the firehub project'))->camel();
     *
     * // 'theFirehubProject'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To get the words of the string.
     * @uses \FireHub\Runtime\Arr\Structure::slice() To slice the array.
     * @uses \FireHub\Runtime\Arr\Transform::map() To map the array.
     * @uses \FireHub\Runtime\Str\MB\Casing::capitalize() To capitalize the first character of the string.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To implode the array.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to camel case.
     */
    public function camel ():CaseTransformable {

        $words = $this->words();

        return new $this->str(
            $words === []
                ? ''
                : $words[0].(Runtime\Arr\Structure::slice($words, 1) // @phpstan-ignore offsetAccess.notFound
                        |> (static fn(array $x): array => Runtime\Arr\Transform::map(
                            $x,
                            static fn(string $word): string => Runtime\Str\MB\Casing::capitalize($word)))
                        |> (static fn(array $x): string => Runtime\Str\SB\Delimiter::implode($x))
                    ),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts extracted words to pascal case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the firehub project'))->pascal();
     *
     * // 'TheFirehubProject'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To get the words of the string.
     * @uses \FireHub\Runtime\Arr\Transform::map() To map the array.
     * @uses \FireHub\Runtime\Str\MB\Casing::capitalize() To capitalize the first character of the string.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To implode the array.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to pascal case.
     */
    public function pascal ():CaseTransformable {

        return new $this->str(
            Runtime\Str\SB\Delimiter::implode(
                Runtime\Arr\Transform::map(
                    $this->words(),
                    static fn(string $word):string => Runtime\Str\MB\Casing::capitalize($word)
                )
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to snake case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the firehub project'))->snake();
     *
     * // 'the_firehub_project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To get the words of the string.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To implode the array.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to snake case.
     */
    public function snake ():CaseTransformable {

        return new $this->str(
            Runtime\Str\SB\Delimiter::implode(
                $this->words(),
                '_'
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to a macro case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the firehub project'))->macro();
     *
     * // 'THE_FIREHUB_PROJECT'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Case\Converter::snake() To convert the string to snake case.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::case() To convert the string to uppercase.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::upper() To convert the string to uppercase.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to macro case.
     */
    public function macro ():CaseTransformable {

        return $this->snake()->case()->upper();

    }

    /**
     * ### Converts the string to kebab case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the firehub project'))->kebab();
     *
     * // 'the-firehub-project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To get the words of the string.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To implode the array.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to kebab case.
     */
    public function kebab ():CaseTransformable {

        return new $this->str(
            Runtime\Str\SB\Delimiter::implode(
                $this->words(),
                '-'
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to a cobol case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the firehub project'))->cobol();
     *
     * // 'THE-FIREHUB-PROJECT'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Case\Converter::kebab() To convert the string to kebab case.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::case() To convert the string to uppercase.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::upper() To convert the string to uppercase.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to cobol case.
     */
    public function cobol ():CaseTransformable {

        return $this->kebab()->case()->upper();

    }

    /**
     * ### Converts the string to a dot case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the firehub project'))->dot();
     *
     * // 'the.firehub.project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To get the words of the string.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To implode the array.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to dot case.
     */
    public function dot ():CaseTransformable {

        return new $this->str(
            Runtime\Str\SB\Delimiter::implode(
                $this->words(),
                '.'
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to an alternate case
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the firehub project'))->alternate();
     *
     * // 'tHe fIrEhUb pRoJeCt'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Runtime\Str\MB\Inspection::length() To get the length of the string.
     * @uses \FireHub\Runtime\Str\MB\Access::part() To get the character at the specified position.
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the string to an alternate case.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::LOWER To convert the string to an alternate case.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::UPPER To convert the string to an alternate case.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to an alternate case.
     */
    public function alternate ():CaseTransformable {

        $result = ''; $upper = false;

        $length = Runtime\Str\MB\Inspection::length(
            $this->str->value(),
            $this->str->encoding()
        );

        for ($i = 0; $i < $length; $i++) {

            $char = Runtime\Str\MB\Access::part(
                $this->str->value(),
                $i,
                1,
                $this->str->encoding()
            );

            if (Runtime\Str\MB\Casing::convert(

                    $char,
                    CaseMode::LOWER,
                    $this->str->encoding()
                ) !== $char) {

                $result .= $char;

                continue;

            }

            $result .= $upper
                ? Runtime\Str\MB\Casing::convert(
                    $char,
                    CaseMode::UPPER,
                    $this->str->encoding()
                )
                : Runtime\Str\MB\Casing::convert(
                    $char,
                    CaseMode::LOWER,
                    $this->str->encoding()
                );

            $upper = !$upper;

        }

        return new $this->str($result, $this->str->encoding());

    }

    /**
     * ### Converts the string into a human-readable format
     *
     * Replaces word separators with spaces and converts the string into sentence-style text.
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('the_firehub_project'))->humanize();
     *
     * // 'The firehub project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To extract words from the string.
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the first word to uppercase.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join words.
     * @uses \FireHub\Runtime\Str\MB\Casing::capitalize() To capitalize the first character of the string.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return \FireHub\Foundation\Str\Boundary\CaseTransformable Returns a new instance with the string converted
     * to a human-readable format.
     */
    public function humanize ():CaseTransformable {

        $words = $this->words();

        return new $this->str(
            $words === []
                ? ''
                : Runtime\Str\MB\Casing::capitalize(
                Runtime\Str\SB\Delimiter::implode($words, ' ')
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Extracts words from the string
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Tokenizer::words() To get the words of the string.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return string[] Returns an array of words extracted from the string.
     */
    private function words ():array {

        return new Tokenizer($this->str->value(), $this->str->encoding())->words();

    }

}