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
use FireHub\Core\Type\Temporal\DateTime as BaseDateTime;
use FireHub\Foundation\Temporal\Exception\InvalidDateTimeException;
use DateTimeImmutable;

/**
 * ### Provides an immutable date and time value object with a high-level developer API
 *
 * The DateTime class represents a combined calendar date and time value within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with date and time values while preserving
 * immutable value semantics inherited from the Core Value Object system.
 *
 * The class is responsible for high-level date and time operations and developer experience, while low-level date and
 * time functionality remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\Temporal\DateTime<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class DateTime extends BaseDateTime {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param DateTimeImmutable $value <p>
     * The date and time value in the normalized Y-m-d H:i:s.u format.
     * </p>
     *
     * @return void
     */
    protected function __construct (
        protected DateTimeImmutable $value
    ) {}

    /**
     * ### Creates a datetime value from a formatted string
     *
     * Parses the given time value using the specified format and creates a new immutable Time value object.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * // '2000-01-01 12:00:00.000000'
     *
     * $datetime = DateTime::from('01.01.2000 12.00.00', 'd.m.Y H:i:s');
     *
     * // '2000-01-01 12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * The datetime value to parse.
     * </p>
     *
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Temporal\Format $format [optional] <p>
     * The format used to parse the datetime value.
     * </p>
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime value cannot be parsed
     * using the given format.
     *
     * @return static<non-empty-string> A new DateTime instance.
     */
    public static function from (string $value, string|Format $format = Format::ISO_DATE_TIME):static {

        $format = $format instanceof Format ? $format->value : $format;

        $datetime = DateTimeImmutable::createFromFormat('!'.$format, $value);

        $errors = DateTimeImmutable::getLastErrors();

        if (
            $datetime === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        ) throw new InvalidDateTimeException("DateTime value could not be parsed using the $format format.");

        return new static($datetime);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->value();
     *
     * // '2000-01-01 12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Meta\Enum\Temporal\Format::ISO_DATE_TIME_MICROSECONDS As the temporal format.
     *
     * @return non-empty-string Raw VO value.
     */
    public function value ():string {

        return $this->value->format(Format::ISO_DATE_TIME_MICROSECONDS->value);

    }

}