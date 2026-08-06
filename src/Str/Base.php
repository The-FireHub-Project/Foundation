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
use FireHub\Core\Type\Str\Encoding;
use FireHub\Foundation\Convert;
use FireHub\Foundation\Str\Operation\Extract;
use FireHub\Runtime;

/**
 * ### Provides the base abstraction for string value objects
 *
 * Defines the common foundation for all string-based value objects within the FireHub Foundation ecosystem.
 *
 * This base abstraction provides shared behavior, immutable value handling, and common string semantics while
 * allowing specialized string implementations to extend and customize their own capabilities.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Core\Type\Str<TValue>
 *
 * @phpstan-consistent-constructor
 */
abstract readonly class Base extends Str {

    /**
     * ### Default encoding
     * @since 1.0.0
     */
    protected const Encoding DEFAULT_ENCODING = Encoding::UTF_8;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * The string value.
     * </p>
     * @param \FireHub\Core\Type\Str\Encoding $encoding [optional] <p>
     * The encoding of the string.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected string $value,
        protected Encoding $encoding = self::DEFAULT_ENCODING
    ) {}

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->value();
     *
     * // FireHub
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $encoding = Str::of('FireHub')->encoding();
     *
     * // Encoding::UTF_8
     * </code>
     *
     * @uses \FireHub\Runtime\Str\MB\Configuration::encoding() To get the default encoding.
     *
     * @since 1.0.0
     *
     */
    public function encoding ():Encoding {

        return $this->encoding;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $string = Str::of('FireHub')->withEncoding(Encoding::ASCII);
     *
     * $encoding = $string->encoding();
     *
     * // Encoding::ASCII
     * </code>
     *
     * @since 1.0.0
     */
    public function withEncoding (Encoding $encoding):static {

        return new static($this->value, $encoding);

    }

    /**
     * ### Creates a new Convert instance for the current string value
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('123')->convert()->strict()->int();
     *
     * // 123
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Convert Returns a new instance of the Convert class.
     */
    public function convert ():Convert {

        return new Convert($this->value);

    }

    /**
     * ### Creates a new Extract instance for the current string value
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Str\Operation\Extract<$this> Returns a new instance of the Extract class.
     */
    public function extract ():Extract {

        return new Extract($this);

    }

    /**
     * ### Checks if the string starts with a given value
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->startsWith('F');
     *
     * // true
     *
     * $string = Str::of('FireHub')->startsWith('');
     *
     * // true
     * </code>
     *
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
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->startsWithAny(['D', 'F', 'H']);
     *
     * // true
     *
     * $string = Str::of('FireHub')->startsWithAny(['D', 'G', 'H']);
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Base::startsWith() To check if the string starts with any of the given values.
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
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->endsWith('b');
     *
     * // true
     *
     * $string = Str::of('FireHub')->endsWith('');
     *
     * // true
     * </code>
     *
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
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->endsWithAny(['v', 'b', 'n']);
     *
     * // true
     *
     * $string = Str::of('FireHub')->endsWithAny(['v', 'm', 'n']);
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Base::endsWith() To check if the string ends with any of the given values.
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
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->contains('ire');
     *
     * // true
     *
     * $string = Str::of('FireHub')->contains('test');
     *
     * // false
     * </code>
     *
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
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->containsAll(['i', 'r', 'e']);
     *
     * // true
     *
     * $string = Str::of('FireHub')->containsAll(['i', 'r', 'e', 's']);
     *
     * // false
     * </code>
     *
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
     * ### Finds the first occurrence of a substring in the string
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('The FireHub Project')->indexOf('F');
     *
     * // 4
     *
     * $string = Str::of('The FireHub Project')->indexOf('x');
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Search::firstPosition() To find the first occurrence of the substring.
     *
     * @param string $find <p>
     * A string to find position.
     * </p>
     *
     * @return false|non-negative-int Numeric position of the first occurrence or false if none exist.
     */
    public function indexOf (string $find):int|false {

        return Runtime\Str\MB\Search::firstPosition($find, $this->value, encoding: $this->encoding);

    }

    /**
     * ### Finds the last occurrence of a substring in the string
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('The FireHub Project')->lastIndexOf('t');
     *
     * // 18
     *
     * $string = Str::of('The FireHub Project')->lastIndexOf('x');
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Search::lastPosition() To find the last occurrence of the substring.
     *
     * @param string $find <p>
     * A string to find position.
     * </p>
     *
     * @return false|non-negative-int Numeric position of the last occurrence or false if none exist.
     */
    public function lastIndexOf (string $find):int|false {

        return Runtime\Str\MB\Search::lastPosition($find, $this->value, encoding: $this->encoding);

    }

    /**
     * ### Prepends a string to the current string
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->prepend('The (FireHub∖Foundation∖Str');
     *
     * // 'The FireHub' (FireHub∖Foundation∖Str
     * </code>
     *
     * @since 1.0.0
     *
     * @param string $value <p>
     * The string to prepend.
     * </p>
     *
     * @return static<string> Returns a new instance with the string prepended.
     */
    public function prepend (string $value):static {

        return new static($value.$this->value, $this->encoding);

    }

    /**
     * ### Appends a string to the current string
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->append(' Project');
     *
     * // 'FireHub Project' (FireHub∖Foundation∖Str
     * </code>
     *
     * @since 1.0.0
     *
     * @param string $value <p>
     * The string to prepend.
     * </p>
     *
     * @return static<string> Returns a new instance with the string appeended.
     */
    public function append (string $value):static {

        return new static($this->value.$value, $this->encoding);

    }

    /**
     * ### Cleans up the string by removing unwanted characters and whitespace
     *
     * This method performs various transformations on the string to clean it up and make it suitable for display or
     * other purposes. It replaces certain characters with their equivalents, trims whitespace, and removes zero-width
     * characters.
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('“  The   FireHub Project…  ”')->tidy();
     *
     * // " The FireHub Project... " (FireHub\Foundation\Str)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Transform::trim() To trim the string.
     * @uses \FireHub\Runtime\Str\MB\Regex::replace() To replace certain characters with their equivalents.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If an error occurred while performing a regular
     * expression search and replace type.
     * @throws \FireHub\Runtime\Exception\InvalidEncodingException If string is not valid for the current encoding.
     *
     * @return static<string> Returns a new instance with the string cleaned up.
     */
    public function tidy ():static {

        $replacements = [
            // Ellipsis
            '\x{2026}' => '...',

            // Smart quotes
            '[\x{201C}\x{201D}]' => '"',
            '[\x{2018}\x{2019}]' => "'",

            // Dashes
            '[\x{2013}\x{2014}]' => '-',

            // Non-breaking space
            '\x{00A0}' => ' ',

            // Zero-width characters
            '[\x{200B}-\x{200D}\x{FEFF}]' => '',

            // Whitespace
            '[[:space:]]+' => ' ',
        ];


        $value = $this->value;

        foreach ($replacements as $pattern => $replacement) {
            $value = Runtime\Str\MB\Regex::replace(
                $pattern,
                $replacement,
                $value
            );
        }

        return new static(
            Runtime\Str\MB\Transform::trim(
                $value,
                encoding: $this->encoding
            ),
            $this->encoding
        );

    }

    /**
     * ### Inserts a string at a specified position in the current string
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('The FireHub Project')->insert('-', 4);
     *
     * // 'The -FireHub Project' (FireHub\Foundation\Str)
     *
     * $string = Str::of('The FireHub Project')->insert('-', -4);
     *
     * // 'The FireHub Pro-ject' (FireHub\Foundation\Str)
     *
     * $string = Str::of('The FireHub Project')->insert('-', 100);
     *
     * // 'The FireHub Project-' (FireHub\Foundation\Str)
     *
     * $string = Str::of('The FireHub Project')->insert('-', -100);
     *
     * // '-The FireHub Project' (FireHub\Foundation\Str)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Operation\Extract::slice() To extract the string at the specified position.
     * @uses \FireHub\Foundation\Str\Base::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Base::append() To append the string to the current string.
     *
     * @param string $string <p>
     * The string to insert.
     * </p>
     * @param int $position <p>
     * The position at which to insert the string.
     * </p>
     *
     * @return static<string> Returns a new instance with the string inserted at the specified position.
     */
    public function insert (string $string, int $position):static {

        return new static(
            $this->extract()->slice(0, $position)->value(),
            $this->encoding
        )
            ->append($string)
            ->append($this->extract()->slice($position)->value());

    }

    /**
     * ### Moves a portion of the string to another position
     *
     * Removes a substring from the given position and inserts it at the target position.
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->move(4, 2, 1);
     *
     * // 'FHuireb' (FireHub\Foundation\Str)
     *
     * $string = Str::of('FireHub')->move(4, 2, -2);
     *
     * // 'FirHueb' (FireHub\Foundation\Str)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Operation\Extract::slice() To extract the substring to move.
     * @uses \FireHub\Runtime\Str\MB\Access::part() To move the substring.
     * @uses \FireHub\Foundation\Str\Base::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Base::append() To append the string to the current string.
     *
     * @param int $from <p>
     * The starting character position of the substring to move.
     * </p>
     * @param int $length <p>
     * The number of characters to move.
     * </p>
     * @param int $to <p>
     * The target character position where the substring will be inserted.
     * </p>
     *
     * @return static<string> A new instance with the moved portion.
     */
    public function move (int $from, int $length, int $to):static {

        $part = $this->extract()->slice($from, $length)->value();

        $remaining = $this->extract()->slice(0, $from)
            ->append($this->extract()->slice($from + $length)->value())->value;

        return new static(
            Runtime\Str\MB\Access::part(
                $remaining,
                0,
                $to,
                $this->encoding()
            ),
            $this->encoding()
        )
            ->append($part)
            ->append(Runtime\Str\MB\Access::part(
                $remaining,
                $to,
                null,
                $this->encoding()
            ));

    }

    /**
     * ### Replaces a substring with a new value
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('The FireHub Project')->overwrite(4, 11, 'Awesome');
     *
     * // 'The Awesome Project' (FireHub\Foundation\Str)
     *
     * $string = Str::of('The FireHub Project')->overwrite(-15, -8, 'Awesome');
     *
     * // 'The Awesome Project' (FireHub\Foundation\Str)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Access::part() To replace the substring.
     * @uses \FireHub\Foundation\Str\Base::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Base::append() To append the string to the current string.
     *
     * @param int $from <p>
     * The starting character position of the substring to replace.
     * </p>
     * @param int $until <p>
     * The ending character position of the substring to replace.
     * </p>
     * @param string $with <p>
     * The new value to replace the substring with.
     * </p>
     *
     * @since 1.0.0
     *
     * @return static<string> A new instance with the replaced substring.
     */
    public function overwrite (int $from, int $until, string $with):static {

        return new static(
            Runtime\Str\MB\Access::part(
                $this->value(),
                0,
                $from,
                $this->encoding()
            ),
            $this->encoding()
        )
            ->append($with)
            ->append(Runtime\Str\MB\Access::part(
                $this->value(),
                $until,
                null,
                $this->encoding()
            ));

    }

}