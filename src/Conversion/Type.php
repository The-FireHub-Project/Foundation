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

namespace FireHub\Foundation\Conversion;

/**
 * ### Abstract Converter Base Class
 *
 * Provides a shared foundation for all FireHub converter implementations by centralizing common converter behavior.
 *
 * The abstract class stores the original input value and defines the common structure inherited by type-specific
 * converters such as StringConverter, ArrayConverter, IntegerConverter, and other future converter implementations.
 *
 * Responsibilities:
 * - Store the source value immutably.
 * - Provide access to the original value.
 * - Define shared converter behavior independent of a specific target type.
 * - Reduce duplicated constructor and state management logic across converters.
 *
 * The class does not perform type-specific conversions. Concrete converters are responsible for implementing their
 * own conversion rules.
 *
 * This abstraction ensures consistency, scalability, and maintainability across the FireHub Runtime converter system.
 * @since 1.0.0
 */
abstract readonly class Type {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param mixed $value <p>
     * The value to convert.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected mixed $value
    ) {}

    /**
     * ### Converts the value to the target type
     * @since 1.0.0
     *
     * @return mixed The converted value.
     */
    abstract public function convert ():mixed;

}