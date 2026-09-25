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
 * ### Provides access to pattern-based string operations
 *
 * Serves as the entry point for performing regular expression operations on immutable string value objects.
 *
 * This class exposes specialized pattern components for matching, collecting, replacing, and transforming string
 * content while keeping pattern logic separated from the underlying string representation.
 * @since 1.0.0
 */
final readonly class All extends Expression {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected function regex (string $pattern):string {

        return $pattern;

    }

}