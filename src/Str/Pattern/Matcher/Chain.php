<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Foundation
 */

namespace FireHub\Foundation\Str\Pattern\Matcher;

use FireHub\Foundation\Str\Pattern\Matcher;

/**
 * ### Provides a fluent chain for combining multiple string-matching conditions
 *
 * Allows defining multiple matching rules that are evaluated together against a string value.
 *
 * Each condition is added to the chain and executed as a single validation process, returning whether all defined
 * conditions are satisfied.
 * @since 1.0.0
 *
 * @method self custom (string $pattern) Adds a custom condition to the chain.
 * @method self is (string $pattern) Adds a condition to check if the string is equal to the specified value.
 * @method self contains (string $pattern) Adds a condition to check if the string contains the specified value.
 * @method self startsWith (string $pattern) Adds a condition to check if the string starts with the specified value.
 * @method self endsWith (string $pattern) Adds a condition to check if the string ends with the specified value.
 * @method self before (string $pattern) Adds a condition to check if the string is before the specified value.
 * @method self after (string $pattern) Adds a condition to check if the string is after the specified value.
 */
final class Chain {

    /**
     * ### The list of conditions to be evaluated
     * @since 1.0.0
     *
     * @var callable[]
     */
    private array $checks = [];

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Str\Pattern\Matcher $matcher <p>
     * The matcher to use for the chain.
     * </p>
     *
     * @return void
     */
    public function __construct(
        private readonly Matcher $matcher
    ) {}

    /**
     * ### Executes the chain of conditions
     * @since 1.0.0
     *
     * @return bool Returns true if all conditions are satisfied, false otherwise.
     */
    public function execute ():bool {

        foreach ($this->checks as $check)
            if (!$check()) return false;

        return true;

    }

    /**
     * ### Allows chaining of method calls to the matcher
     * @since 1.0.0
     *
     * @param string $method <p>
     * The method name to call on the matcher.
     * </p>
     * @param array $arguments <p>
     * The arguments to pass to the method.
     * </p>
     *
     * @return $this The current instance of the Chain class.
     */
    public function __call (string $method, array $arguments):self {

        $this->checks[] = fn():bool => $this->matcher->$method(...$arguments);

        return $this;

    }

}