<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\Str\Boundary;

use FireHub\Foundation\Str\Pattern;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Defines the contract for strings supporting pattern-based operations
 *
 * Provides a capability contract for string value objects that support regular expression-based matching, replacing,
 * and splitting operations.
 *
 * Implementations expose access to pattern processing while keeping the underlying string value immutable.
 * @since 1.0.0
 */
interface Patternable {

    /**
     * ### Returns the pattern object for this string
     * @since 1.0.0
     *
     * @param \FireHub\Runtime\Type\Str\RegexDelimiter $delimiter [optional] <p>
     * The delimiter to use for pattern matching.
     * </p>
     * @param \FireHub\Runtime\Type\Str\RegexFlag ...$flags [optional] <p>
     * The regex flags to apply to the pattern operation.
     * </p>
     *
     * @return \FireHub\Foundation\Str\Pattern<$this> Returns a new instance of the Pattern class.
     */
    public function pattern (RegexDelimiter $delimiter = RegexDelimiter::SLASH, RegexFlag ...$flags):Pattern;

}