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

namespace FireHub\Foundation\Temporal;

use FireHub\Core\Meta\Enum\Temporal\Format;
use FireHub\Core\Type\Temporal\Time as BaseTime;
use FireHub\Foundation\Temporal\Exception\InvalidTimeException;
use DateTimeImmutable;

/**
 * ### Provides an immutable time value object with a high-level developer API
 *
 * The Time class represents a time of day within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with time values while preserving immutable
 * value semantics inherited from the Core Value Object system.
 *
 * The class is responsible for high-level time operations and developer experience, while low-level date and time
 * functionality remains delegated to the native PHP date and time API.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\Temporal\Time<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Time extends BaseTime {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param DateTimeImmutable $value <p>
     * The time value in the normalized H:i:s.u format.
     * </p>
     *
     * @return void
     */
    protected function __construct (
        protected DateTimeImmutable $value
    ) {}

    /**
     * ### Creates a time value from a formatted string
     *
     * Parses the given time value using the specified format and creates a new immutable Time value object.
     *
     * <code>
     * use FireHub\Foundation\Temporal\Time;
     *
     * $time = Time::from('12:00:00');
     *
     * // '12:00:00.000000'
     *
     * $time = Time::from('12.00.00.000000', 'H.i.s.u');
     *
     * // '12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * The time value to parse.
     * </p>
     *
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Temporal\Format $format [optional] <p>
     * The format used to parse the time value.
     * </p>
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeException If the time value cannot be parsed using
     * the given format.
     *
     * @return static<non-empty-string> A new Time instance.
     */
    public static function from (string $value, string|Format $format = Format::ISO_TIME):static {

        $format = $format instanceof Format ? $format->value : $format;

        $time = DateTimeImmutable::createFromFormat('!'.$format, $value);

        $errors = DateTimeImmutable::getLastErrors();

        if (
            $time === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        ) throw new InvalidTimeException("Time value could not be parsed using the $format format.");

        return new static($time);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Time;
     *
     * $time = Time::from('12:00:00')->value();
     *
     * // '12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Meta\Enum\Temporal\Format::ISO_TIME_MICROSECONDS As the temporal format.
     *
     * @return non-empty-string Raw VO value.
     */
    public function value ():string {

        return $this->value->format(Format::ISO_TIME_MICROSECONDS->value);

    }

}