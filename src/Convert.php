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

namespace FireHub\Foundation;

use FireHub\Foundation\Conversion\Policy\ {
    Safe, Strict
};

/**
 * ### Provides a fluent API for value conversion
 *
 * Provides a unified entry point for converting values into supported native representations.
 *
 * The Convert class exposes different conversion policies and delegates the actual conversion logic to dedicated
 * types. It separates conversion behavior from failure handling, allowing callers to choose whether failed
 * conversions should throw exceptions or return nullable results.
 *
 * This class acts as the high-level developer API for the FireHub Foundation conversion system.
 * @since 1.0.0
 */
final readonly class Convert {

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
        private mixed $value
    ) {}

    /**
     * ### Performs safe value conversions
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Conversion\Policy\Safe Returns a new instance of the Safe conversion policy.
     */
    public function safe ():Safe {

        return new Safe($this->value);

    }

    /**
     * ### Performs strict value conversions
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Conversion\Policy\Strict Returns a new instance of the Strict conversion policy.
     */
    public function strict ():Strict {

        return new Strict($this->value);

    }

}