<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.3
 * @package Foundation
 */

namespace FireHub\Foundation\Str\Pattern;

use FireHub\Core\Type\ {
    Char, Str
};
use FireHub\Foundation\Str\Boundary\Patternable;
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Provides the common foundation for pattern-based string operations
 *
 * Defines the shared base implementation for components that perform regular expression operations on immutable
 * string value objects.
 *
 * This abstraction centralizes access to the underlying string instance, allowing specialized implementations such
 * as matching, replacing, and splitting to share common behavior while remaining focused on their individual
 * responsibilities.
 * @since 1.0.0
 *
 * @template TPatternable of \FireHub\Foundation\Str\Boundary\Patternable
 *
 * @phpstan-type StringValue (Char<non-empty-string>&TPatternable)|(Str<string>&TPatternable)
 */
abstract readonly class Base {

    /**
     * ### The default regex flags to apply to pattern operations
     * @since 1.0.0
     *
     * @var \FireHub\Runtime\Type\Str\RegexFlag[]
     */
    protected const array DEFAULT_FLAGS = [
        RegexFlag::MULTIBYTE
    ];

    /**
     * ### The delimiter to use for pattern matching
     * @since 1.0.0
     *
     * @var \FireHub\Runtime\Type\Str\RegexDelimiter
     */
    protected RegexDelimiter $delimiter;

    /**
     * ### The regex flags to apply to the pattern operation
     * @since 1.0.0
     *
     * @var \FireHub\Runtime\Type\Str\RegexFlag[]
     */
    protected array $flags;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::values() To remove duplicate flags.
     * @uses \FireHub\Runtime\Arr\Transform::unique() To remove duplicate flags.
     * @uses \FireHub\Foundation\Str\Pattern\Base::DEFAULT_FLAGS To set the default flags.
     *
     * @param StringValue $str <p>
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
     *
     * @note RegexFlag::MULTIBYTE is always applied by default to ensure proper handling of multibyte characters in
     * pattern operations.
     */
    public function __construct (
        protected (Str&Patternable)|(Char&Patternable) $str,
        RegexDelimiter $delimiter = RegexDelimiter::SLASH,
        RegexFlag ...$flags
    ) {

        $this->delimiter = $delimiter;

        $this->flags = Runtime\Arr\Access::values(
            Runtime\Arr\Transform::unique([
                ...self::DEFAULT_FLAGS,
                ...$flags
            ])
        );

    }

    /**
     * ### Performs the pattern matching operation
     * @since 1.0.0
     *
     * @param string $pattern <p>
     * The pattern to match.
     * </p>
     *
     * @return mixed The result of the pattern matching operation.
     */
    abstract public function custom (string $pattern):mixed;

    /**
     * ### Builds the pattern to use for the operation
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To build the modifiers string.
     * @uses \FireHub\Runtime\Arr\Transform::map() To map the flags to their string representations.
     *
     * @param string $pattern <p>
     * The pattern to build.
     * </p>
     *
     * @return non-empty-string The built pattern.
     */
    protected function patternBuilder (string $pattern):string {

        $modifiers = Runtime\Str\SB\Delimiter::implode(
            Runtime\Arr\Transform::map(
                $this->flags,
                static fn(RegexFlag $flag): string => $flag->value
            )
        );

        return $this->delimiter->value . $pattern . $this->delimiter->value . $modifiers;

    }

}