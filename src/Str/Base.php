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

}