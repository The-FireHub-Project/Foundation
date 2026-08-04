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
 * ### Matches the entire string against a pattern
 *
 * Creates a regular expression that requires the provided pattern to match the complete input string.
 *
 * The generated expression anchors the pattern to the beginning and end of the subject, ensuring that partial
 * matches are not considered successful.
 * @since 1.0.0
 */
final class Is implements Expression {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function regex (string $pattern):string {

        return '\A'.$pattern.'\z';

    }

}