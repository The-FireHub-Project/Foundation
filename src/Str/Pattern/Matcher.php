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
    Contains, EndsWith, Is, StartsWith
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

        var_dump($this->patternBuilder($pattern));

        return Runtime\Str\SB\Regex::match(
            $this->patternBuilder($pattern),
            $this->str->value(),
            $this->offset
        );

    }

    /**
     * ### Checks if the string is the specified pattern
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->is()->custom('the firehub project');
     *
     * // true
     *
     * $string = new Matcher(new Str('the firehub project'))->is()->custom('firehub');
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Str\Pattern\Expression\Is New Is instance.
     */
    public function is ():Is {

        return new Is($this);

    }

    /**
     * ### Checks if the string contains the specified pattern
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->contains()->custom('firehub');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Str\Pattern\Expression\Contains New Contains instance.
     */
    public function contains ():Contains {

        return new Contains($this);

    }

    /**
     * ### Checks if the string starts with the specified pattern
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->startsWith()->custom('the');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return StartsWith New StartsWith instance.
     */
    public function startsWith ():StartsWith {

        return new StartsWith($this);

    }

    /**
     * ### Checks if the string ends with the specified pattern
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Matcher;
     * use FireHub\Foundation\Str;
     *
     * $string = new Matcher(new Str('the firehub project'))->endsWith()->custom('project');
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return EndsWith New EndsWith instance.
     */
    public function endsWith ():EndsWith {

        return new EndsWith($this);

    }

}