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

namespace FireHub\Foundation\Number;

use FireHub\Core\Type\Number\Integer as BaseInteger;
use FireHub\Foundation\Conversion\Policy\Strict;

/**
 * ### Provides an immutable integer value object with a high-level developer API
 *
 * The Integer class represents an integer value within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with a whole number of values while preserving
 * immutable value semantics inherited from the Core Value Object system.
 *
 * The class implements the Core numeric type contract and extends the base Number Value Object abstraction, allowing
 * integer values to be used consistently across the FireHub ecosystem.
 *
 * This class is responsible for high-level integer operations and developer experience, while low-level numeric
 * execution remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of int
 *
 * @extends \FireHub\Core\Type\Number\Integer<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Integer extends BaseInteger {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * The integer value.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected int $value
    ) {}

    /**
     * ### Creates a new integer instance from a given value
     *
     * <code>
     * use FireHub\Foundation\Integer;
     *
     * $int = Integer::of('1');
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Policy\Strict::int() To convert the value to a integer.
     *
     * @param mixed $value <p>
     * The value to convert to a integer.
     * </p>
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     * @throws \FireHub\Runtime\Exception\InvalidPatternException If the regular expression pattern is invalid.
     *
     * @return static<int> A new Integer instance representing the integer value.
     */
    public static function of (mixed $value):static {

        return new static(new Strict($value)->int());

    }

    /**
     * ### Converts the integer value to a real number
     *
     * <code>
     * use FireHub\Foundation\Integer;
     *
     * $real = new Integer(10)->toReal();
     *
     * // 10.0
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Number\Real<float> Returns a new instance of the Real class.
     */
    public function toReal ():Real {

        return new Real((float)$this->value);

    }

    /**
     * ### Converts the integer value to a decimal number
     *
     * <code>
     * use FireHub\Foundation\Integer;
     *
     * $decimal = new Integer(10)->toDecimal();
     *
     * // '10'
     * </code>
     *
     * @since 1.0.0
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Number\Exception\InvalidFractionalException If the value is not a valid decimal
     * number.
     *
     * @return \FireHub\Foundation\Number\Decimal<numeric-string> Returns a new instance of the Decimal class.
     */
    public function toDecimal ():Decimal {

        /** @var \FireHub\Foundation\Number\Decimal<numeric-string> */
        return new Decimal((string)$this->value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Integer;
     *
     * $string = new Integer(10)->value();
     *
     * // 10
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():int {

        return $this->value;

    }

}