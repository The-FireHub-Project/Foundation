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

use FireHub\Core\Type\Boolean as BaseBoolean;
use FireHub\Foundation\Conversion\Policy\Strict;

/**
 * ### Provides an immutable boolean value object with a high-level developer API
 *
 * The Boolean class represents a boolean value within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with boolean values while preserving immutable
 * value semantics inherited from the Core Value Object system.
 *
 * The class implements the Core boolean type contract and extends the base Value Object abstraction, allowing boolean
 * values to be used consistently across the FireHub ecosystem.
 *
 * This class is responsible for high-level boolean operations and developer experience, while low-level value handling
 * remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of bool
 *
 * @extends \FireHub\Core\Type\Boolean<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Boolean extends BaseBoolean {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * The boolean value.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected bool $value
    ) {}

    /**
     * ### Creates a new boolean instance from a given value
     *
     * <code>
     * use FireHub\Foundation\Boolean;
     *
     * $boolean = Boolean::of(1);
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Policy\Strict::bool() To convert the value to a boolean.
     *
     * @param mixed $value <p>
     * The value to convert to a boolean.
     * </p>
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     *
     * @return static<bool> A new Boolean instance representing the bool value.
     */
    public static function of (mixed $value):static {

        return new static(new Strict($value)->bool());

    }

    /**
     * ### Checks if the value is true
     *
     * <code>
     * use FireHub\Foundation\Boolean;
     *
     * $boolean = new Boolean(true)->isTrue();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool Returns true if the value is true, false otherwise.
     */
    public function isTrue ():bool {

        return $this->value === true;

    }
    /**
     * ### Checks if the value is false
     *
     * <code>
     * use FireHub\Foundation\Boolean;
     *
     * $boolean = new Boolean(false)->isFalse();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool Returns true if the value is false, false otherwise.
     */
    public function isFalse ():bool {

        return $this->value === false;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Boolean;
     *
     * $boolean = new Boolean(true)->value();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @phpstan-ignore-next-line
     */
    public function value ():bool {

        return $this->value;

    }

}