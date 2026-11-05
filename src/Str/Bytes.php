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

namespace FireHub\Foundation\Str;

use FireHub\Core\Type\Str;
use FireHub\Core\Meta\Enum\Side;
use FireHub\Core\Foundation\Constant\Numeric\IntegerLimits;
use FireHub\Foundation\Char\Byte;
use FireHub\Runtime;

/**
 * ### Provides an immutable binary string value object
 *
 * The Bytes class represents an immutable sequence of bytes within the FireHub Foundation layer.
 *
 * Unlike text-oriented string value objects, this class operates directly on raw bytes without interpreting
 * character encodings.
 *
 * Each byte can contain any value between 0 and 255, including null bytes and values that do not represent valid
 * text characters.
 *
 * All positions and lengths are measured in bytes rather than characters.
 *
 * This class is suitable for binary data, encoded content, network payloads, file contents, and other byte-oriented
 * operations.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Core\Type\Str<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Bytes extends Str {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * The raw binary string.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {}

    /**
     * ### Checks if the string starts with a given value
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the string starts with the given value.
     *
     * @param string $value
     *
     * @return bool True if the string starts with the given value, false otherwise.
     *
     * @caution Empty strings are considered to start with any string.
     */
    public function startsWith (string $value):bool {

        return Runtime\Str\SB\Search::startsWith($value, $this->value);

    }

    /**
     * ### Checks if the string starts with any of the given values
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::startsWith() To check if the string starts with any of the given values.
     *
     * @param iterable<string> $values <p>
     * The values to check.
     * </p>
     *
     * @return bool True if the string starts with any of the given values, false otherwise.
     */
    public function startsWithAny (iterable $values):bool {

        foreach ($values as $value)
            if ($this->startsWith($value)) return true;

        return false;

    }

    /**
     * ### Checks if the string ends with a given value
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if the string ends with the given value.
     *
     * @param string $value <p>
     * The value to check for.
     * </p>
     *
     * @return bool True if the string starts with the given value, false otherwise.
     *
     * @caution Empty string are considered to start with any string.
     */
    public function endsWith (string $value):bool {

        return Runtime\Str\SB\Search::endsWith($value, $this->value);

    }

    /**
     * ### Checks if the string ends with any of the given values
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::endsWith() To check if the string ends with any of the given values.
     *
     * @param iterable<string> $values <p>
     * The values to check.
     * </p>
     *
     * @return bool True if the string ends with any of the given values, false otherwise.
     */
    public function endsWithAny (iterable $values):bool {

        foreach ($values as $value)
            if ($this->endsWith($value)) return true;

        return false;

    }

    /**
     * ### Checks if the string contains a given value
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check if the string contains the given value.
     *
     * @param string $value <p>
     * The value to check for.
     * </p>
     *
     * @return bool True if the string contains the given value, false otherwise.
     */
    public function contains (string $value):bool {

        return Runtime\Str\SB\Search::contains($value, $this->value);

    }

    /**
     * ### Checks if the string contains all the given values
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str::contains() To check if the string contains all of the given values.
     *
     * @param iterable<string> $values <p>
     * The values to check for.
     * </p>
     *
     * @return bool True if the string contains all of the given values, false otherwise.
     */
    public function containsAll (iterable $values):bool {

        $has_value = false;

        foreach ($values as $value) {

            $has_value = true;

            if (!$this->contains($value)) return false;

        }

        return $has_value;

    }

    /**
     * ### Get string length
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the string length.
     *
     * @return non-negative-int String length.
     */
    public function length ():int {

        return Runtime\Str\SB\Inspection::length($this->value);

    }

    /**
     * ### Finds the first occurrence of a substring in the string
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the first occurrence of the substring.
     *
     * @param string $find <p>
     * A string to find position.
     * </p>
     *
     * @return false|non-negative-int Numeric position of the first occurrence or false if none exist.
     */
    public function indexOf (string $find):int|false {

        return Runtime\Str\SB\Search::firstPosition($find, $this->value);

    }

    /**
     * ### Finds the last occurrence of a substring in the string
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::lastPosition() To find the last occurrence of the substring.
     *
     * @param string $find <p>
     * A string to find position.
     * </p>
     *
     * @return false|non-negative-int Numeric position of the last occurrence or false if none exist.
     */
    public function lastIndexOf (string $find):int|false {

        return Runtime\Str\SB\Search::lastPosition($find, $this->value);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::split() To split the string into an array of characters.
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException If the $length parameter is less than 1.
     * @throws \FireHub\Foundation\Char\Exception\InvalidLengthException If the value does not contain exactly one byte.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function toChars ():array {

        $chars = [];

        foreach ($this->split() as $char)
            $chars[] = new Byte($char);

        return $chars;

    }

    /**
     * ### Given a string, return an array of its characters
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Access::split() To split the string into an array of characters.
     *
     * @param positive-int $length [optional] <p>
     * The length of each character.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException If the $length parameter is less than 1.
     *
     * @return list<non-empty-string> Returns an array of characters.
     */
    public function split (int $length = 1):array {

        return Runtime\Str\SB\Access::split($this->value, $length);

    }

    /**
     * ### Given a string, return an array of its characters grouped into chunks of a given size
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::split() To split the string into chunks.
     * @uses \FireHub\Foundation\Str\Bytes::length() To get the string length.
     * @uses \FireHub\Runtime\Math::ceil() To calculate the number of chunks.
     *
     * @param positive-int $number_of_groups<p>
     * The number of groups to split the string into.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException If the $length parameter is less than 1.
     *
     * @return list<non-empty-string> Grouped string into chunks.
     */
    public function group (int $number_of_groups):array {

        return $this->split((
            $size = Runtime\Math::ceil($this->length() / $number_of_groups)) >= 1 ? $size : 1
        );

    }

    /**
     * ### Split a string by a string
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the string by the given separator.
     *
     * @param non-empty-string $separator <p>
     * The boundary string.
     * </p>
     * @param int<min, max> $limit [optional] <p>
     * If the limit is set and positive, the returned array will contain a maximum of limit elements with the last
     * element containing the rest of the string.
     *
     * If the limit parameter is negative, all components except the last – limit are returned.
     *
     * If the limit parameter is zero, then this is treated as 1.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\EmptySeparatorException If the separator is an empty string.
     *
     * @return list<string> Returns an array of substrings.
     */
    public function explode (string $separator, int $limit = IntegerLimits::MAX):array {

        return Runtime\Str\SB\Delimiter::explode($this->value, $separator, $limit);

    }

    /**
     * ### Prepends a string to the current string
     * @since 1.0.0
     *
     * @param string $value <p>
     * The string to prepend.
     * </p>
     *
     * @return static<string> Returns a new instance with the string prepended.
     */
    public function prepend (string $value):static {

        return new static($value.$this->value);

    }

    /**
     * ### Appends a string to the current string
     * @since 1.0.0
     *
     * @param string $value <p>
     * The string to prepend.
     * </p>
     *
     * @return static<string> Returns a new instance with the string appeended.
     */
    public function append (string $value):static {

        return new static($this->value.$value);

    }

    /**
     * ### Ensures that the string starts with a given prefix
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::startsWith() To check if the string starts with the given prefix.
     * @uses \FireHub\Foundation\Str\Bytes() To prepend the prefix to the string if it does not start with it.
     *
     * @param string $prefix <p>
     * The string to prepend.
     * </p>
     *
     * @return static<string>|static<TValue> Returns a new instance with the string prepended if it does not start
     * with the prefix.
     */
    public function ensurePrefix (string $prefix):self {

        if ($this->startsWith($prefix))
            return new static(
                $this->value
            );

        return $this->prepend($prefix);

    }

    /**
     * ### Ensures that the string ends with a given suffix
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::endsWith() To check if the string ends with the given suffix.
     * @uses \FireHub\Foundation\Str\Bytes::append() To append the suffix to the string if it does not ends with it.
     *
     * @param string $suffix <p>
     * The string to append.
     * </p>
     *
     * @return static<string>|static<TValue> Returns a new instance with the string appended if it does not ends with
     * the prefix.
     */
    public function ensureSuffix (string $suffix):self {

        if ($this->endsWith($suffix))
            return new static(
                $this->value
            );

        return $this->append($suffix);

    }

    /**
     * ### Surrounds the string with a given string
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::prepend() To prepend the given string to the current string.
     * @uses \FireHub\Foundation\Str\Bytes::append() To append the given string to the current string.
     *
     * @param string $with <p>
     * The string to surround the current string with.
     * </p>
     *
     * @return static<string> Returns a new instance with the string surrounded by the given string.
     */
    public function surround (string $with):static {

        return $this->prepend($with)->append($with);

    }

    /**
     * ### Repeats a string a specified number of times
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::repeat() To repeat the string.
     * @uses \FireHub\Foundation\Str\Bytes::value() To get the string value.
     *
     * @param int $times <p>
     * The number of times to repeat the string.
     * </p>
     * @param string $separator [optional] <p>
     * The separator to use between repeated strings.
     * </p>
     *
     * @return static<string> Returns a new instance with the repeated string.
     */
    public function repeat (int $times, string $separator = ''):self {

        return new static(
            Runtime\Str\SB\Transform::repeat($this->value(), $times, $separator)
        );

    }

    /**
     * ### Duplicated a substring with another string
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::repeat() To repeat the string.
     *
     * @param int $times <p>
     * The number of times to duplicate the string.
     * </p>
     * @param string $separator [optional] <p>
     * The separator to use between duplicate strings.
     * </p>
     *
     * @return static<string> Returns a new instance with the duplicated string.
     */
    public function duplicate (int $times, string $separator = ''):self {

        return $this->repeat($times + 1, $separator);

    }

    /**
     * ### Reverses the string
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Bytes::split() To split the string into an array of characters.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To reverse the string.
     * @uses \FireHub\Runtime\Arr\Transform::reverse() To reverse the string.
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException If the $length parameter is less than 1.
     *
     * @return static<string> Returns a new instance with the reversed string.
     */
    public function reverse ():static {

        return new static(
            Runtime\Str\SB\Delimiter::implode(Runtime\Arr\Transform::reverse($this->split()))
        );

    }

    /**
     * ### Pads the string to a specified length with a specified character
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::pad() To pad the string.
     *
     * @param int $length <p>
     * If the value of $length is negative, less than, or equal to the length of the input string, no padding takes
     * place.
     * </p>
     * @param non-empty-string $pad [optional] <p>
     * The pad may be truncated if the required number of padding characters can't be evenly divided by the pad's
     * length.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Side $side [optional] <p>
     * Pad side.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\EmptyPadException If the pad is empty.
     *
     * @return static<string> Returns a new instance with the string padded to a specified length.
     */
    public function pad (int $length, string $pad = ' ', Side $side = Side::RIGHT):static {

        return new static(
            Runtime\Str\SB\Transform::pad($this->value, $length, $pad, $side),
        );

    }

    /**
     * ### Strip whitespace (or other characters) from the beginning and end of a string
     *
     * This function returns a string with whitespace stripped from the beginning and end of the string.
     *
     * Without the second parameter, SB::trim() will strip these characters.
     *
     * - " " (ASCII 32 (0x20)), an ordinary space.
     * - "\t" (ASCII 9 (0x09)), a tab.
     * - "\n" (ASCII 10 (0x0A)), a new line (line feed).
     * - "\r" (ASCII 13 (0x0D)), a carriage return.
     * - "\0" (ASCII 0 (0x00)), the NUL-byte.
     * - "\v" (ASCII 11 (0x0B)), a vertical tab.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the string.
     *
     * @param \FireHub\Core\Meta\Enum\Side $side [optional] <p>
     * Side to trim string.
     * </p>
     * @param string $characters [optional] <p>
     * The stripped characters can also be specified using the char-list parameter.
     *
     * List all characters that you want to be stripped. With '..', you can specify a range of characters.
     * </p>
     *
     * @return static<string> Returns a new instance with the string trimmed.
     */
    public function trim (Side $side = Side::BOTH, string $characters = " \n\r\t\v\x00"):static {

        return new static(
            Runtime\Str\SB\Transform::trim($this->value, $side, $characters)
        );

    }

    /**
     * ### Shuffles the characters in the string
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Ordering::shuffle() To shuffle the characters in the string.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To implode the shuffled characters into a string.
     * @uses \FireHub\Foundation\Str\Bytes::split() To split the string into an array of characters.
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException If the $length parameter is less than 1.
     *
     * @return static<string> Returns a new instance with the shuffled string.
     */
    public function shuffle ():static {

        $characters = $this->split();

        Runtime\Arr\Ordering::shuffle($characters);

        return new static(
            Runtime\Str\SB\Delimiter::implode($characters),
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

}