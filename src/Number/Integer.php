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
use FireHub\Runtime;

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

    /**
     * ### Adds a value to the current integer value
     *
     * <code>
     * use FireHub\Foundation\Integer;
     *
     * $integer = new Integer(10)->add(20);
     *
     * // 30
     * </code>
     *
     * @since 1.0.0
     *
     * @param int|self<int> $value <p>
     * The value to add to the current value.
     * </p>
     *
     * @return static<int> Returns a new instance of the Integer class with the sum of the current value and the given
     * value.
     */
    public function add (int|self $value):static {

        return new static(
            $this->value + ($value instanceof self ? $value->value() : $value)
        );

    }

    /**
     * ### Subtracts a value to the current integer value
     *
     * <code>
     * use FireHub\Foundation\Integer;
     *
     * $integer = new Integer(10)->subtract(20);
     *
     * // -10
     * </code>
     *
     * @since 1.0.0
     *
     * @param int|self<int> $value <p>
     * The value to subtract to the current value.
     * </p>
     *
     * @return static<int> Returns a new instance of the Integer class with the difference of the current value and the
     * given value.
     */
    public function subtract (int|self $value):static {

        return new static(
            $this->value - ($value instanceof self ? $value->value() : $value)
        );

    }

    /**
     * ### Multiplies the current integer value by a value
     *
     * <code>
     * use FireHub\Foundation\Integer;
     *
     * $integer = new Integer(10)->multiply(20);
     *
     * // 200
     * </code>
     *
     * @since 1.0.0
     *
     * @param int|self<int> $value <p>
     * The value to multiply the current value by.
     * </p>
     *
     * @return static<int> Returns a new instance of the Integer class with the product of the current value and the
     * given value.
     */
    public function multiply (int|self $value):static {

        return new static(
            $this->value * ($value instanceof self ? $value->value() : $value)
        );

    }

    /**
     * ### Divides the current integer value by a value
     *
     * <code>
     * use FireHub\Foundation\Integer;
     *
     * $integer = new Integer(10)->divide(2);
     *
     * // 5
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math::divideInt() To divide the current value by the given value.
     *
     * @param non-zero-int|self<non-zero-int> $value <p>
     * The value to divide the current value by.
     * </p>
     *
     * @return static<int> Returns a new instance of the Integer class with the quotient of the current value and the
     * given value.
     */
    public function divide (int|self $value):static {

        return new static(
            Runtime\Math::divideInt($this->value, $value instanceof self ? $value->value() : $value)
        );

    }

    /**
     * ### Raises the current integer value to a power
     *
     * <code>
     * use FireHub\Foundation\Number\Integer;
     *
     * $integer = new Integer(2)->power(3);
     *
     * // 8
     * </code>
     *
     * @since 1.0.0
     *
     * @param int|self<int> $exponent <p>
     * The exponent to raise the current value to.
     * </p>
     *
     * @return static<int> Returns a new Integer instance with the result.
     */
    public function power (int|self $exponent):static {

        return new static(
            $this->value ** ($exponent instanceof self ? $exponent->value() : $exponent)
        );

    }

}