<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.4
 * @package Foundation
 */

namespace FireHub\Foundation\Temporal;

use FireHub\Core\Type\Temporal\ {
    DateTime as BaseDateTime, Timezone
};
use FireHub\Core\Type\Date\Zone;
use FireHub\Core\Meta\Enum\Date\Format;
use FireHub\Foundation\Temporal\Exception\ {
    InvalidDateTimeException, InvalidTimeZoneException
};
use DateMalformedStringException, DateInvalidTimeZoneException, DateTimeImmutable, DateTimeZone;

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
     * ### Creates a new DateTime instance from a given string
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * // '2000-01-01 12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $datetime <p>
     * A date/time string.
     * </p>
     * @param null|\FireHub\Core\Type\Temporal\Timezone<non-empty-string> $timezone [optional] <p>
     * The timezone used to parse the datetime value.
     * </p>
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime string is invalid.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     *
     * @return static<non-empty-string> Returns a new DateTime instance.
     */
    public static function from (string $datetime, ?Timezone $timezone = null):static {

        try {

            $datetime = new DateTimeImmutable(
                $datetime,
                new DateTimeZone($timezone?->value() ?? Zone::UTC->value)
            );

        } catch (DateMalformedStringException) {

            throw new InvalidDateTimeException;

        } catch (DateInvalidTimeZoneException) {

            throw new InvalidTimeZoneException;

        }

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
     * @uses \FireHub\Core\Meta\Enum\Date\Format::ISO_DATE_TIME_MICROSECONDS As the temporal format.
     *
     * @return non-empty-string Raw VO value.
     */
    public function value ():string {

        return $this->value->format(Format::ISO_DATE_TIME_MICROSECONDS->value);

    }

}