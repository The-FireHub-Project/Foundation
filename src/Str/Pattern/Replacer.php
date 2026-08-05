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
use FireHub\Foundation\Str\Pattern\Expression\All;
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Replaces string content using regular expressions
 *
 * Provides functionality for transforming strings by replacing content that matches regular expression patterns.
 *
 * This class supports pattern-based replacements while preserving immutable string handling and delegating regular
 * expression execution to the Runtime layer.
 * @since 1.0.0
 *
 * @template TPatternable of \FireHub\Foundation\Str\Boundary\Patternable
 *
 * @extends \FireHub\Foundation\Str\Pattern\Base<TPatternable>
 */
final readonly class Replacer extends Base {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Str<string>&TPatternable $str <p>
     * The string value to operate on.
     * </p>
     * @param string $with <p>
     * The replacement string.
     * </p>
     * @param int $limit <p>
     * The maximum number of replacements to perform. Use -1 for no limit.
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
        private string $with,
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
     * use FireHub\Foundation\Str\Pattern\Replacer;
     * use FireHub\Foundation\Str;
     *
     * $string = new Replacer(new Str('the firehub project'), 'firehub', -1)->custom('project');
     *
     * // 'the firehub firehub' (FireHub\Foundation\Str)
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::replace() For pattern replacement.
     * @uses \FireHub\Foundation\Str\Pattern\Base::patternBuilder() To build the regular expression pattern.
     * @uses \FireHub\Core\Type\Str::of() To create a new Str instance with the replaced content.
     * @uses \FireHub\Foundation\Str\Boundary\Patternable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\Patternable::encoding() To get the string encoding.
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If error while performing a regular expression,
     * search and replace.
     *
     * @return \FireHub\Core\Type\Str<string>&TPatternable A new Str instance with the replaced content.
     */
    public function custom (string $pattern):Str {

        return $this->str::of(
            Runtime\Str\SB\Regex::replace(
                $this->patternBuilder($pattern),
                $this->with,
                $this->str->value(),
                $this->limit
            ),
            $this->str->encoding()
        );

    }

    /**
     * ### Replace the string contains the specified pattern before a given substring
     *
     * <code>
     * use FireHub\Foundation\Str\Pattern\Replacer;
     * use FireHub\Foundation\Str;
     *
     * $string = new Replacer(new Str('the firehub project'), 'replaced', -1)->all()->custom('firehub');
     *
     * // 'the replaced project' (FireHub\Foundation\Str)
     *
     * $string = new Replacer(new Str('the firehub project'), 'replaced', -1)->all()->custom('the');
     *
     * // 'replaced firehub project' (FireHub\Foundation\Str)
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Str\Pattern\Expression\All New All instance.
     */
    public function all ():All {

        return new All($this);

    }

}