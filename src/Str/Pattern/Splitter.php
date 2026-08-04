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
 * ### Splits strings using regular expressions
 *
 * Provides functionality for dividing strings into multiple segments based on regular expression patterns.
 *
 * This class handles pattern-based string separation while keeping tokenization and parsing concerns isolated from
 * string value objects.
 * @since 1.0.0
 *
 * @template TPatternable of \FireHub\Foundation\Str\Boundary\Patternable
 *
 * @extends \FireHub\Foundation\Str\Pattern\Base<TPatternable>
 */
final readonly class Splitter extends Base {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Str<string>&TPatternable $str <p>
     * The string value to operate on.
     * </p>
     * @param bool $remove_empty <p>
     * Whether to remove empty segments from the result.
     * </p>
     * @param int $limit <p>
     * The maximum number of segments to return.
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
        private bool $remove_empty,
        private int $limit,
        RegexDelimiter $delimiter = RegexDelimiter::SLASH,
        RegexFlag ...$flags
    ) {

        parent::__construct($str, $delimiter, ...$flags);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Splitter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Splitter(new Str('the firehub project'), false, -1)->custom('firehub');
     *
     * // ['the ', ' project']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::split() To perform the regular expression split.
     * @uses \FireHub\Foundation\Str\Pattern\Base::patternBuilder() To build the regular expression pattern.
     * @uses \FireHub\Foundation\Str\Boundary\Patternable::value() To get the string value.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If error while performing a regular expression,
     * search and replace.
     *
     * @return list<string> A new array of strings with the split content.
     */
    public function custom (string $pattern):array {

        return Runtime\Str\SB\Regex::split(
            $this->patternBuilder($pattern),
            $this->str->value(),
            $this->limit,
            $this->remove_empty
        );

    }

    /**
     * ### Split the string contains the specified pattern before a given substring
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Splitter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Splitter(new Str('the firehub project'), false, -1)->before('firehub');
     *
     * // ['the ', 'firehub project']
     *
     * $string = new Splitter(new Str('the firehub project'), false, -1)->before('the');
     *
     * // ['', 'the firehub project']
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
     * @return list<string> A new array of strings with the split content.
     */
    public function before (string $pattern):array {

        return $this->custom(new Before()->regex($pattern));

    }

    /**
     * ### Split the string contains the specified pattern after a given substring
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Splitter;
     * use FireHub\Foundation\Str;
     *
     * $string = new Splitter(new Str('the firehub project'), false, -1)->after('firehub');
     *
     * // ['the firehub', ' project']
     *
     * $string = new Splitter(new Str('the firehub project'), false, -1)->after('project');
     *
     * // [the firehub project', '']
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
     * @return list<string> A new array of strings with the split content.
     */
    public function after (string $pattern):array {

        return $this->custom(new After()->regex($pattern));

    }

}