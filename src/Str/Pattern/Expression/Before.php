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

namespace FireHub\Foundation\Str\Pattern\Expression;

use FireHub\Foundation\Str\Pattern\Expression;

/**
 * ### Matches content occurring before a pattern
 *
 * Creates a regular expression that identifies a position immediately before the specified pattern.
 *
 * The expression uses a positive lookahead to assert that the target pattern exists after the matched location
 * without including the pattern itself in the result.
 * @since 1.0.0
 */
final class Before implements Expression {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function regex (string $pattern):string {

        return '(?='.$pattern.')';

    }

}