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
 */
final readonly class Converter {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Str<string>&TCaseTransformable $str <p>
     * The string to convert.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Str&CaseTransformable $str
    ) {}

    /**
     * ### Capitalizes the first character of the string
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('firehub'))->capitalize();
     *
     * // Firehub
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::capitalize() To capitalize the first character of the string.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @return TCaseTransformable Returns a new instance with the first character capitalized.
     */
    public function capitalize ():CaseTransformable {

        return $this->str::of(
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
     * // fireHub
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::uncapitalize() To Uncapitalize the first character of the string.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @return TCaseTransformable Returns a new instance with the first character uncapitalized.
     */
    public function uncapitalize ():CaseTransformable {

        return $this->str::of(
            Runtime\Str\MB\Casing::uncapitalize($this->str->value()),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to lowercase
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('FireHub'))->lower();
     *
     * // firehub
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the string to lowercase.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::LOWER To convert the string to lowercase.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to lowercase.
     */
    public function lower ():CaseTransformable {

        return $this->str::of(
            Runtime\Str\MB\Casing::convert(
                $this->str->value(),
                CaseMode::LOWER,
                $this->str->encoding()
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Converts the string to uppercase
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('FireHub'))->upper();
     *
     * // FIREHUB
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the string to uppercase.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::UPPER To convert the string to uppercase.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted to uppercase.
     */
    public function upper ():CaseTransformable {

        return $this->str::of(
            Runtime\Str\MB\Casing::convert(
                $this->str->value(),
                CaseMode::UPPER,
                $this->str->encoding()
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Swaps the case of all alphabetic characters
     *
     * Converts lowercase characters to uppercase and uppercase characters to lowercase while leaving
     * non-alphabetic characters unchanged.
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Converter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Converter(new Str('The FireHub Project'))->swap();
     *
     * // tHE fIREhUB pROJECT
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::UPPER To convert the string to uppercase.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::LOWER To convert the string to lowercase.
     * @uses \FireHub\Runtime\Str\MB\Inspection::length() To get string length.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get part of the string.
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To swap lowercase and uppercase.
     * @uses \FireHub\Runtime\Str\MB\Transform::upper() To convert lowercase characters to uppercase.
     * @uses \FireHub\Runtime\Str\MB\Transform::lower() To convert uppercase characters to lowercase.
     *
     * @return \FireHub\Foundation\Str\Boundary\CaseTransformable Returns a new instance with the string swaped to
     * kebab case.
     */
    public function swap ():CaseTransformable {

        $result = '';

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

            $lower = Runtime\Str\MB\Casing::convert(
                $char,
                CaseMode::LOWER,
                $this->str->encoding()
            );

            $upper = Runtime\Str\MB\Casing::convert(
                $char,
                CaseMode::UPPER,
                $this->str->encoding()
            );

            $result .= match ($char) {
                $lower => $upper,
                $upper => $lower,
                default => $char,
            };

        }

        return $this->str::of($result, $this->str->encoding());

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
     * // The Firehub
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the string the title case.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::UPPER To convert the string the title case.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     *
     * @return TCaseTransformable Returns a new instance with the string converted the title case.
     */
    public function title ():CaseTransformable {

        return $this->str::of(
            Runtime\Str\MB\Casing::convert(
                $this->str->value(),
                CaseMode::TITLE,
                $this->str->encoding()
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
     * // theFirehubProject
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
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
     * @return \FireHub\Foundation\Str\Boundary\CaseTransformable Returns a new instance with the string converted to
     * camel case.
     */
    public function camel ():CaseTransformable {

        $words = $this->words();

        return $this->str::of(
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
     * // TheFirehubProject
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
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
     * @return \FireHub\Foundation\Str\Boundary\CaseTransformable Returns a new instance with the string converted to
     * pascal case.
     */
    public function pascal ():CaseTransformable {

        return $this->str::of(
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
     * // the_firehub_project
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To get the words of the string.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To implode the array.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return \FireHub\Foundation\Str\Boundary\CaseTransformable Returns a new instance with the string converted to
     * snake case.
     */
    public function snake ():CaseTransformable {

        return $this->str::of(
            Runtime\Str\SB\Delimiter::implode(
                $this->words(),
                '_'
            ),
            $this->str->encoding()
        );

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
     * // the-firehub-project
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::of() To create a new Str instance.
     * @uses \FireHub\Foundation\Str\Boundary\CaseTransformable::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Case\Converter::words() To get the words of the string.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To implode the array.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type, or regular expression split.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return \FireHub\Foundation\Str\Boundary\CaseTransformable Returns a new instance with the string converted to
     * kebab case.
     */
    public function kebab ():CaseTransformable {

        return $this->str::of(
            Runtime\Str\SB\Delimiter::implode(
                $this->words(),
                '-'
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