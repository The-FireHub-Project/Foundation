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

namespace FireHub\Foundation\Str\Operation;

use FireHub\Foundation\Str\Base;
use FireHub\Runtime;

/**
 * ### Extracts portions of strings using delimiters and boundaries
 *
 * This class provides methods for extracting specific parts of a string based on defined delimiters, markers, or
 * boundaries. It allows retrieving content before, after, between, or up to specified values while preserving the
 * original string structure.
 * @since 1.0.0
 *
 * @template TBase of \FireHub\Foundation\Str\Base
 */
final readonly class Extract {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TBase $base <p>
     * The string to extract from.
     * </p>
     * @param bool $case_sensitive [optional] <p>
     * Whether the extraction should be case-sensitive.
     * Defaults to true.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Base $base,
        private bool $case_sensitive = true
    ) {}

    /**
     * ### Extracts a portion of a string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The FireHub Project'))->slice(2);
     *
     * // 'e FireHub Project'
     *
     * $string = new Extract(new Str('The FireHub Project'))->slice(4, 2);
     *
     * // 'Fi'
     *
     * $string = new Extract(new Str('The FireHub Project'))->slice(-5, -1);
     *
     * // 'ojec'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Access::part() To extract the portion of the string.
     * @uses \FireHub\Foundation\Str\Base::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     *
     * @param int $from <p>
     * If start is non-negative, the returned string will start at the start position in string, counting from zero.
     *
     * For instance, in the string 'abcdef', the character at position 0 is 'a', the character at position 2 is 'c',
     * and so forth.
     *
     * If the start is negative, the returned string will start at the start character from the end of the string.
     * </p>
     * @param null|int $length [optional] <p>
     * Maximum number of characters to use from string.
     * If omitted or NULL is passed, extract all characters to the end of the string.
     * </p>
     *
     * @return TBase
     */
    public function slice (int $from, ?int $length = null):Base {

        return new $this->base(
            Runtime\Str\MB\Access::part(
                $this->base->value(),
                $from,
                $length,
                $this->base->encoding()
            ),
            $this->base->encoding()
        );

    }

    /**
     * ### Extracts the first occurrence of a string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The FireHub Project'))->from('Fi');
     *
     * // 'FireHub Project'
     *
     * $string = new Extract(new Str('The FireHub Project'))->from('test');
     *
     * // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Access::firstOccurrence() To extract the first occurrence of the string.
     * @uses \FireHub\Foundation\Str\Base::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     *
     * @param string $find <p>
     * The string to search for.
     * </p>
     *
     * @return TBase The first occurrence of the string in the string.
     */
    public function from (string $find):Base {

        return new $this->base(
            ($result = Runtime\Str\MB\Access::firstOccurrence(
                $this->base->value(),
                $find,
                case_sensitive: $this->case_sensitive,
                encoding: $this->base->encoding()
            )) === false ? '' : $result,
            $this->base->encoding()
        );

    }

    /**
     * ### Extracts the last occurrence of a string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The FireHub Project, The FireHub Project'))->fromLast('FireHub');
     *
     * // 'FireHub Project'
     *
     * $string = new Extract(new Str('The FireHub Project, The FireHub Project'))->fromLast('test');
     *
     * // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Access::lastCharacter() To extract the last occurrence of the string.
     * @uses \FireHub\Foundation\Str\Base::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     *
     * @param string $find <p>
     * The string to search for.
     * </p>
     *
     * @return TBase The last occurrence of the string in the string.
     */
    public function fromLast (string $find):Base {

        return new $this->base(
            ($result = Runtime\Str\MB\Access::lastCharacter(
                $this->base->value(),
                $find,
                case_sensitive: $this->case_sensitive,
                encoding: $this->base->encoding()
            )) === false ? '' : $result,
            $this->base->encoding()
        );

    }

    /**
     * ### Extracts the portion of a string up to a given string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The FireHub Project'))->until('Fi');
     *
     * // 'The '
     *
     * $string = new Extract(new Str('The FireHub Project'))->until('test');
     *
     * // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Base::indexOf() To get the index of the string.
     * @uses \FireHub\Foundation\Str\Operation\Extract::slice() To extract the portion of the string.
     *
     * @param string $find <p>
     * The string to search for.
     * </p>
     *
     * @return TBase The portion of the string up to the given string.
     */
    public function until (string $find):Base {

        $position = $this->base->indexOf($find);

        return $this->slice(0, $position === false ? 0 : $position);

    }

    /**
     * ### Extracts the portion of a string up to the last occurrence of a string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The FireHub Project, The FireHub Project'))->untilLast('Fi');
     *
     * // 'The FireHub Project, The '
     *
     * $string = new Extract(new Str('The FireHub Project, The FireHub Project'))->untilLast('test');
     *
     * // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Base::lastIndexOf() To get the last index of the string.
     * @uses \FireHub\Foundation\Str\Operation\Extract::slice() To extract the portion of the string.
     *
     * @param string $find <p>
     * The string to search for.
     * </p>
     *
     * @return TBase The portion of the string up to the last occurrence of the given string.
     */
    public function untilLast (string $find):Base {

        $position = $this->base->lastIndexOf($find);

        return $this->slice(0, $position === false ? 0 : $position);

    }

    /**
     * ### Extracts the portion of a string after a given string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The FireHub Project'))->after('The ');
     *
     * // 'FireHub Project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Operation\Extract::slice() To extract the portion of the string.
     * @uses \FireHub\Runtime\Str\MB\Inspection::length() To get the length of the string.
     * @uses \FireHub\Foundation\Str\Base::indexOf() To get the index of the string.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     *
     * @param string $find <p>
     * The string to search for.
     * </p>
     *
     * @return TBase The portion of the string after the given string.
     */
    public function after (string $find):Base {

        $position = $this->base->indexOf($find);

        if ($position === false) return $this->slice(0, 0);

        return $this->slice(
            $position + Runtime\Str\MB\Inspection::length(
                $find,
                $this->base->encoding()
            )
        );

    }

    /**
     * ### Extracts the portion of a string after the last occurrence of a string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The FireHub Project, The FireHub Project'))->after('The ');
     *
     * // 'FireHub Project'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Operation\Extract::slice() To extract the portion of the string.
     * @uses \FireHub\Runtime\Str\MB\Inspection::length() To get the length of the string.
     * @uses \FireHub\Foundation\Str\Base::lastIndexOf() To get the last index of the string.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     *
     * @param string $find <p>
     * The string to search for.
     * </p>
     *
     * @return TBase The portion of the string after the last occurrence of the given string.
     */
    public function afterLast (string $find):Base {

        $position = $this->base->lastIndexOf($find);

        if ($position === false) return $this->slice(0, 0);

        return $this->slice(
            $position + Runtime\Str\MB\Inspection::length(
                $find,
                $this->base->encoding()
            )
        );

    }

    /**
     * ### Extracts the portion of a string between two strings
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The [FireHub] Project, The FireHub [Project]'))->between('[', ']');
     *
     * // 'FireHub] Project, The FireHub [Project'
     *
     * $string = new Extract(new Str('The FireHub Project, The FireHub Project'))->between('[', ']');
     *
     * // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Operation\Extract after() To extract the portion of the string.
     * @uses \FireHub\Foundation\Str\Operation\Extract untilLast() To extract the portion of the string.
     *
     * @param string $start <p>
     * The starting string.
     * </p>
     * @param string $end <p>
     * The ending string.
     * </p>
     *
     * @return TBase The portion of the string between the given strings.
     */
    public function between (string $start, string $end):Base {

        return $this->after($start)->extract()->untilLast($end);

    }

    /**
     * ### Extracts the portion of a string between the first occurrence of two strings
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The [FireHub] Project, The FireHub [Project]'))->betweenFirst('[', ']');
     *
     * // 'FireHub'
     *
     * $string = new Extract(new Str('The FireHub Project, The FireHub Project'))->betweenFirst('[', ']');
     *
     * // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Operation\Extract after() To extract the portion of the string.
     * @uses \FireHub\Foundation\Str\Operation\Extract until() To extract the portion of the string.
     *
     * @param string $start <p>
     * The starting string.
     * </p>
     * @param string $end <p>
     * The ending string.
     * </p>
     *
     * @return TBase The portion of the string between the first occurrence of the given strings.
     */
    public function betweenFirst (string $start, string $end):Base {

        return $this->after($start)->extract()->until($end);

    }

    /**
     * ### Extracts the portion of a string between the last occurrence of two strings
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Extract;
     * use FireHub\Foundation\Str;
     *
     * $string = new Extract(new Str('The [FireHub] Project, The FireHub [Project]'))->betweenLast('[', ']');
     *
     * // 'FireHub'
     *
     * $string = new Extract(new Str('The FireHub Project, The FireHub Project'))->betweenLast('[', ']');
     *
     * // ''
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Operation\Extract afterLast() To extract the portion of the string.
     * @uses \FireHub\Foundation\Str\Operation\Extract untilLast() To extract the portion of the string.
     *
     * @param string $start <p>
     * The starting string.
     * </p>
     * @param string $end <p>
     * The ending string.
     * </p>
     *
     * @return TBase The portion of the string between the last occurrence of the given strings.
     */
    public function betweenLast (string $start, string $end):Base {

        return $this->afterLast($start)->extract()->untilLast($end);

    }

}