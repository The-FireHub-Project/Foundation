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

namespace FireHub\Foundation\Str\Pattern;

use FireHub\Core\Type\Str;
use FireHub\Foundation\Str\Boundary\Patternable;
use FireHub\Foundation\Str\Pattern\Expression\ {
    After, Before
};
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Retrieves all pattern matches from strings
 *
 * Provides functionality for extracting all occurrences that match a regular expression pattern from a string value.
 *
 * This class wraps multi-match regular expression operations and returns collected matched values while preserving
 * string value object semantics.
 * @since 1.0.0
 *
 * @template TPatternable of \FireHub\Foundation\Str\Boundary\Patternable
 *
 * @extends \FireHub\Foundation\Str\Pattern\Base<TPatternable>
 */
final readonly class Getter extends Base {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Str<string>&TPatternable $str <p>
     * The string value to operate on.
     * </p>
     * @param int $offset <p>
     * The offset to start matching from.
     * </p>
     * @param \FireHub\Runtime\Type\Str\RegexDelimiter $delimiter [optional] <p>
     * The delimiter to use for pattern matching.
     * </p>
     * @param \FireHub\Runtime\Type\Str\RegexFlag ...$flags [optional] <p>
     * The regex flags to apply to the pattern operation.
     * </p>
     *
     * @return void
     */
    public function __construct (
        Str&Patternable $str,
        private int $offset,
        RegexDelimiter $delimiter = RegexDelimiter::SLASH,
        RegexFlag ...$flags
    ) {

        parent::__construct($str, $delimiter, ...$flags);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Getter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Getter(new Str('the firehub project'), 0)->custom('firehub');
     *
     * // ['firehub']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To perform the regular expression matching operation.
     * @uses \FireHub\Foundation\Str\Pattern\Base::patternBuilder() To build the regular expression pattern.
     * @uses \FireHub\Core\Type\Str::value() To retrieve the underlying string value for matching.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return array<int, string> The matched groups, or an empty array if no matches were found.
     */
    public function custom (string $pattern):array {

        Runtime\Str\SB\Regex::match(
            $this->patternBuilder($pattern),
            $this->str->value(),
            $this->offset,
            all: true,
            result: $result
        );

        return $result[0] ?? []; // @phpstan-ignore return.type

    }

    /**
     * ### Get the string contains the specified pattern before a given substring
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Getter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Getter(new Str('the firehub project'), 0)->before('firehub');
     *
     * // ['the ']
     *
     * $string = new Getter(new Str('the firehub project'), 0)->before('the');
     *
     * // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Pattern\Matcher::custom() To perform the pattern matching operation.
     *
     * @param string $pattern <p>
     * The pattern to match.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return array<int, string> The matched groups, or an empty array if no matches were found.
     */
    public function before (string $pattern):array {

        return $this->custom('(?:.+)'.new Before()->regex($pattern));

    }

    /**
     * ### Gets the string contains the specified pattern after a given substring
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Getter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Getter(new Str('the firehub project'), 0)->after('firehub');
     *
     * // ['firehub']
     *
     * $string = new Getter(new Str('the firehub project'), 0)->after('project');
     *
     * // []
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Str\Pattern\Matcher::custom() To perform the pattern matching operation.
     *
     * @param string $pattern <p>
     * The pattern to match.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return array<int, string> The matched groups, or an empty array if no matches were found.
     */
    public function after (string $pattern):array {

        return $this->custom(new After()->regex($pattern).'(?:.+)');

    }

}