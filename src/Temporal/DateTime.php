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
use FireHub\Foundation\Temporal\NamedTimezone;
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
     * ### The timezone
     * @since 1.0.0
     */
    protected Timezone $timezone;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param DateTimeImmutable $value <p>
     * The date and time value in the normalized Y-m-d H:i:s.u format.
     * </p>
     * @param null|\FireHub\Core\Type\Temporal\Timezone<non-empty-string> $timezone [optional] <p>
     * The timezone used to parse the datetime value.
     * </p>
     *
     * @return void
     */
    protected function __construct (
        protected DateTimeImmutable $value,
        ?Timezone $timezone = null
    ) {

        $this->timezone = $timezone ?? new NamedTimezone(Zone::UTC);

    }

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

        return new static($datetime, $timezone);

    }

    /**
     * ### Creates a datetime value from a formatted string
     *
     * Parses the given time value using the specified format and creates a new immutable Time value object.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::fromFormat('01.01.2000 12.00.00', 'd.m.Y H.i.s');
     *
     * // '2000-01-01 12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $datetime <p>
     * A date/time string.
     * </p>
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Format $format [optional] <p>
     * The format used to parse the datetime value.
     * </p>
     * @param null|\FireHub\Core\Type\Temporal\Timezone<non-empty-string> $timezone [optional] <p>
     * The timezone used to parse the datetime value.
     * </p>
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime value cannot be parsed
     * using the given format.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     *
     * @return static<non-empty-string> A new DateTime instance.
     */
    public static function fromFormat (string $datetime, string|Format $format = Format::ISO_DATE_TIME, ?Timezone $timezone = null):static {

        $format = $format instanceof Format ? $format->value : $format;

        try {

            $datetime = DateTimeImmutable::createFromFormat(
                $format,
                $datetime,
                new DateTimeZone($timezone?->value() ?? Zone::UTC->value)
            ) ?: throw new InvalidDateTimeException('DateTime value could not be parsed using the format.');

        } catch (DateInvalidTimeZoneException) {

            throw new InvalidTimeZoneException;

        }

        return new static($datetime, $timezone);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->timezone();
     *
     * // NamedTimezone::UTC
     * </code>
     *
     * @since 1.0.0
     */
    public function timezone ():Timezone {

        return $this->timezone;

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

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Foundation\Temporal\NamedTimezone
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->withTimezone(Zone::ARCTIC_LONGYEARBYEN);
     * </code>
     *
     * @since 1.0.0
     */
    public function withTimezone (Timezone $timezone):static {

        return new static($this->value, $timezone);

    }

}