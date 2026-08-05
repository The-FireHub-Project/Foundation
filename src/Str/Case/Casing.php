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
use FireHub\Foundation\Str\Boundary\Caseable;
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\CaseMode;

/**
 * ### Transforms characters between different letter cases
 *
 * Provides reusable character-level case transformation operations for converting alphabetic characters between
 * lowercase and uppercase representations.
 *
 * This class centralizes casing behavior shared by character and string value objects, including lowercase conversion,
 * uppercase conversion, and case swapping while preserving non-alphabetic characters.
 * @since 1.0.0
 *
 * @template TCaseable of \FireHub\Foundation\Str\Boundary\Caseable
 */
final readonly class Casing {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Str<string>&TCaseable $str <p>
     * The string to convert.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Str&Caseable $str
    ) {}

    /**
     * ### Converts the string to lowercase
     *
     * <code>
     * use FireHub\Foundation\Str\Case\Casing;
     * use FireHub\Foundation\Str;
     *
     * $string = new Casing(new Str('FireHub'))->lower();
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
     * @return TCaseable Returns a new instance with the string converted to lowercase.
     */
    public function lower ():Caseable {

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
     * use FireHub\Foundation\Str\Case\Casing;
     * use FireHub\Foundation\Str;
     *
     * $string = new Casing(new Str('FireHub'))->upper();
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
     * @return TCaseable Returns a new instance with the string converted to uppercase.
     */
    public function upper ():Caseable {

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
     * use FireHub\Foundation\Str\Case\Casing;
     * use FireHub\Foundation\Str;
     *
     * $string = new Casing(new Str('The FireHub Project'))->swap();
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
     * @return TCaseable Returns a new instance with the string swaped to kebab case.
     */
    public function swap ():Caseable {

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

}