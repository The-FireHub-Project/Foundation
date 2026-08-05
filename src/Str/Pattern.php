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
use FireHub\Foundation\Str\Boundary\Patternable;
use FireHub\Foundation\Str\Pattern\ {
    Matcher, Replacer
};
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Provides access to pattern-based string operations
 *
 * Serves as the entry point for performing regular expression operations on immutable string value objects.
 *
 * This class groups pattern-related functionality into specialized components, including matching, replacing, and
 * splitting, while delegating low-level execution to the Runtime layer.
 * @since 1.0.0
 *
 * @template TPatternable of \FireHub\Foundation\Str\Boundary\Patternable
 */
final readonly class Pattern {

    /**
     * ### The regex flags to apply to the pattern operation
     * @since 1.0.0
     *
     * @var \FireHub\Runtime\Type\Str\RegexFlag[]
     */
    private array $flags;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Str<string>&TPatternable $str <p>
     * The string value to operate on.
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
        private Str&Patternable $str,
        private RegexDelimiter $delimiter = RegexDelimiter::SLASH,
        RegexFlag ...$flags
    ) {

        $this->flags = $flags;

    }

    /**
     * ### Create a new pattern matcher
     * @since 1.0.0
     *
     * @param int $offset [optional] <p>
     * The offset at which to start the search.
     * </p>
     *
     * @return \FireHub\Foundation\Str\Pattern\Matcher<\FireHub\Core\Type\Str<string>&TPatternable> Returns a new
     * Matcher instance for pattern matching.
     */
    public function match (int $offset = 0):Matcher {

        return new Matcher($this->str, $offset, $this->delimiter, ...$this->flags);

    }

    /**
     * ### Create a new pattern replacer
     * @since 1.0.0
     *
     * @param string $with <p>
     * The replacement string.
     * </p>
     * @param int $limit [optional] <p>
     * The maximum number of replacements to perform. Defaults to -1 (no limit).
     * </p>
     *
     * @return \FireHub\Foundation\Str\Pattern\Replacer<\FireHub\Core\Type\Str<string>&TPatternable> Returns a new
     * Replacer instance for pattern replacement.
     */
    public function replace (string $with, int $limit = -1):Replacer {

        return new Replacer($this->str, $with, $limit, $this->delimiter, ...$this->flags);

    }

    /**
     * ### Create a new pattern removal
     * @since 1.0.0
     *
     * @param int $limit [optional] <p>
     * The maximum number of replacements to perform. Defaults to -1 (no limit).
     * </p>
     *
     * @return \FireHub\Foundation\Str\Pattern\Replacer<\FireHub\Core\Type\Str<string>&TPatternable> Returns a new
     * Replacer instance for pattern removal.
     */
    public function remove (int $limit = -1):Replacer {

        return new Replacer($this->str, '', $limit, $this->delimiter, ...$this->flags);

    }

}