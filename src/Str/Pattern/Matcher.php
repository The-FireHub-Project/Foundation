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
    After, Before, Contains, EndsWith, StartsWith
};
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Matches strings against regular expressions
 *
 * Provides functionality for searching strings using regular expression patterns, including pattern matching, group
 * extraction, and match inspection.
 *
 * This class encapsulates regular expression matching behavior while keeping string manipulation logic separated
 * from low-level execution details.
 * @since 1.0.0
 *
 * @template TPatternable of \FireHub\Foundation\Str\Boundary\Patternable
 *
 * @extends \FireHub\Foundation\Str\Pattern\Base<TPatternable>
 */
final readonly class Matcher extends Base {

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
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->custom('firehub');
     *
     * // true
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
     * @return bool True if the pattern matches, false otherwise.
     */
    public function custom (string $pattern):bool {

        return Runtime\Str\SB\Regex::match(
            $this->patternBuilder($pattern),
            $this->str->value(),
            $this->offset
        );

    }

    /**
     * ### Checks if the string contains the specified pattern
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->contains('firehub');
     *
     * // true
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
     * @return bool True if the pattern matches, false otherwise.
     */
    public function contains (string $pattern):bool {

        return $this->custom(new Contains()->regex($pattern));

    }

    /**
     * ### Checks if the string starts with the specified pattern
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->startsWith('the');
     *
     * // true
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
     * @return bool True if the pattern starts with the specified pattern, false otherwise.
     */
    public function startsWith (string $pattern):bool {

        return $this->custom(new StartsWith()->regex($pattern));

    }

    /**
     * ### Checks if the string ends with the specified pattern
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->endsWith('project');
     *
     * // true
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
     * @return bool True if the pattern ends with the specified pattern, false otherwise.
     */
    public function endsWith (string $pattern):bool {

        return $this->custom(new EndsWith()->regex($pattern));

    }

    /**
     * ### Checks if the string contains the specified pattern before a given substring
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->before('firehub');
     *
     * // true
     *
     * $string = new Matcher(new Str('the firehub project'))->before('the');
     *
     * // false
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
     * @return bool True if the pattern matches before the specified substring, false otherwise.
     */
    public function before (string $pattern):bool {

        return $this->custom('.+'.new Before()->regex($pattern));

    }

    /**
     * ### Checks if the string contains the specified pattern after a given substring
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->after('firehub');
     *
     * // true
     *
     * $string = new Matcher(new Str('the firehub project'))->after('project');
     *
     * // false
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
     * @return bool True if the pattern matches after the specified substring, false otherwise.
     */
    public function after (string $pattern):bool {

        return $this->custom(new After()->regex($pattern).'.+');

    }

}