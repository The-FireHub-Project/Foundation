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

use FireHub\Foundation\Str\Trait\Instantiable;
use FireHub\Foundation\Str\Boundary\Patternable;
use FireHub\Runtime\Type\Str\CaseMode;
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Represents a case-insensitive string value object
 *
 * The Caseless class represents a string value where character casing does not affect comparison semantics.
 *
 * It provides a normalized string representation for case-insensitive operations while preserving the original
 * string value. This allows strings with different casings to be treated as equivalent when performing comparisons,
 * searches, and matching operations.
 *
 * The class is intended for scenarios where textual identity should not depend on uppercase or lowercase characters,
 * such as identifiers, keys, and user-facing text matching.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Foundation\Str\Base<TValue>
 */
readonly class Caseless extends Base implements Patternable {

    /**
     * ### Instantiable
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\Str\Trait\Instantiable<TValue>
     */
    use Instantiable;

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Type\Str\RegexFlag::CASELESS AS included flag.
     */
    public function pattern (RegexDelimiter $delimiter = RegexDelimiter::SLASH, RegexFlag ...$flags):Pattern {

        return new Pattern($this, $delimiter, RegexFlag::CASELESS, ...$flags);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str\Caseless;
     *
     * $string = Caseless::of('FireHub')->startsWith('f');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the string starts with the given value.
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the string to lowercase.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::LOWER To convert the string to lowercase.
     * @uses \FireHub\Foundation\Str\Caseless::comparisonValue() To get the normalized string value.
     */
    public function startsWith (string $value):bool {

        return Runtime\Str\SB\Search::startsWith(
            Runtime\Str\MB\Casing::convert($value, CaseMode::LOWER),
            $this->comparisonValue()
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str\Caseless;
     *
     * $string = Caseless::of('FireHub')->endsWith('B');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if the string ends with the given value.
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the string to lowercase.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::LOWER To convert the string to lowercase.
     * @uses \FireHub\Foundation\Str\Caseless::comparisonValue() To get the normalized string value.
     */
    public function endsWith (string $value):bool {

        return Runtime\Str\SB\Search::endsWith(
            Runtime\Str\MB\Casing::convert($value, CaseMode::LOWER),
            $this->comparisonValue()
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str\Caseless;
     *
     * $string = Caseless::of('FireHub')->contains('reh');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check if the string contains the given value.
     * @uses \FireHub\Runtime\Str\MB\Casing::convert() To convert the string to lowercase.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::LOWER To convert the string to lowercase.
     * @uses \FireHub\Foundation\Str\Caseless::comparisonValue() To get the normalized string value.
     *
     * @param string $value <p>
     * The value to check for.
     * </p>
     *
     * @return bool True if the string contains the given value, false otherwise.
     */
    public function contains (string $value):bool {

        return Runtime\Str\SB\Search::contains(
            Runtime\Str\MB\Casing::convert($value, CaseMode::LOWER),
            $this->comparisonValue()
        );

    }

    /**
     * ### Finds the first occurrence of a substring in the string
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('The FireHub Project')->indexOf('f');
     *
     * // 4
     *
     * $string = Sstr::of('The FireHub Project')->indexOf('x');
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

        return Runtime\Str\MB\Search::firstPosition($find, $this->value, false, encoding: $this->encoding);

    }

    /**
     * ### Finds the last occurrence of a substring in the string
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('The FireHub Project')->lastIndexOf('T');
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

        return Runtime\Str\MB\Search::lastPosition($find, $this->value, false, encoding: $this->encoding);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\MB\Casing::convert To convert the string to lowercase.
     * @uses \FireHub\Runtime\Type\Str\CaseMode::LOWER To convert the string to lowercase.
     */
    protected function comparisonValue ():string {

        return Runtime\Str\MB\Casing::convert($this->value, CaseMode::LOWER);

    }

}