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
use FireHub\Runtime;
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
     *
     * @var \FireHub\Core\Type\Temporal\Timezone<non-empty-string>
     */
    protected Timezone $timezone;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check if the timezone is valid.
     *
     * @param DateTimeImmutable $value <p>
     * The date and time value in the normalized Y-m-d H:i:s.u format.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     *
     * @return void
     */
    protected function __construct (
        protected DateTimeImmutable $value
    ) {

        /** @var non-empty-string $name */
        $name = $value->getTimezone()->getName();

        /** @var \FireHub\Core\Type\Temporal\Timezone<non-empty-string> $timezone */
        $timezone = Runtime\Str\SB\Regex::match('/^[+-]\d{2}:?\d{2}$/', $name) // @phpstan-ignore varTag.nativeType
            ? new FixedTimezone($name)
            : new NamedTimezone(Zone::from($name));

        $this->timezone = $timezone;

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
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime string is invalid.
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
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime value cannot be parsed
     * using the given format.
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

        return new static($datetime);

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

        /** @var Timezone<non-empty-string> */
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
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::from() To create a new DateTime instance with the specified timezone.
     * @uses \FireHub\Foundation\Temporal\DateTime::value() To get the raw value of the DateTime instance.
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime string is invalid.
     */
    public function withTimezone (Timezone $timezone):static {

        /** @var static<TValue> */
        return self::from($this->value(), $timezone);

    }

}