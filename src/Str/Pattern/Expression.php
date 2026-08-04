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

namespace FireHub\Foundation\Str\Pattern;

/**
 * ## Defines a contract for regex pattern expressions
 *
 * Provides a common contract for objects that transform logical pattern expressions into regular expression
 * fragments.
 *
 * Expressions encapsulate reusable regex construction logic, allowing pattern operations such as matching,
 * replacing, and splitting to compose complex expressions without directly handling regular expression syntax.
 * @since 1.0.0
 */
interface Expression {

    /**
     * ### Converts the expression into a regular expression fragment
     * @since 1.0.0
     *
     * @param string $pattern <p>
     * The pattern value to transform into a regular expression fragment.
     * </p>
     *
     * @return string The generated regular expression fragment.
     */
    public function regex (string $pattern):string;

}