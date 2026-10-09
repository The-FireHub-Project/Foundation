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
 * ### Matches a pattern occurring anywhere within the string
 *
 * Wraps the provided pattern with wildcard expressions, allowing the pattern to match regardless of its position
 * within the input string.
 *
 * This expression generates a regular expression fragment that matches any characters before and after the target
 * pattern.
 * @since 1.0.0
 */
final readonly class Contains extends Expression {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function regex (string $pattern):string {

        return '.*'.$pattern.'.*';

    }

}