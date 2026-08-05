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

namespace FireHub\Foundation\Str\Pattern\Expression;

use FireHub\Foundation\Str\Pattern\Expression;

/**
 * ### Matches a pattern at the beginning of the string
 *
 * Creates a regular expression fragment that ensures the provided pattern occurs at the start of the input string.
 * @since 1.0.0
 */
final readonly class StartsWith extends Expression {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function regex (string $pattern):string {

        return '\A'.$pattern;

    }

}