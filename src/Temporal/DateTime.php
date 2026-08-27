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

use FireHub\Core\Type\Temporal\DateTime as BaseDateTime;
use FireHub\Core\Type\Date\Zone;
use FireHub\Core\Meta\Enum\Date\ {
    Format\Token, Format
};
use FireHub\Foundation\Temporal\DateTime\ {
    Accessors, Inspection, Mutators
};
use FireHub\Foundation\Temporal\Exception\InvalidDateTimeException;
use FireHub\Runtime;
use DateMalformedStringException, DateTimeImmutable;

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
 * @phpstan-type TimezoneType \FireHub\Foundation\Temporal\NamedTimezone<value-of<\FireHub\Core\Type\Date\Zone>>|\FireHub\Foundation\Temporal\FixedTimezone<non-empty-string>
 *
 * @phpstan-consistent-constructor
 */
readonly class DateTime extends BaseDateTime {

    /**
     * ### Provides access to the DateTime value object's properties
     * @since 1.0.0
     */
    use Accessors, Inspection, Mutators;

    /**
     * ### The timezone
     * @since 1.0.0
     *
     * @var TimezoneType
     */
    protected NamedTimezone|FixedTimezone $timezone;

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
     * @uses \FireHub\Foundation\Temporal\NamedTimezone::native() To get the native timezone.
     * @uses \FireHub\Core\Type\Date\Zone::UTC To get the UTC timezone.
     *
     * @param non-empty-string $datetime <p>
     * A date/time string.
     * </p>
     * @param null|TimezoneType $timezone [optional] <p>
     * The timezone used to parse the datetime value.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime string is invalid.
     *
     * @return static<non-empty-string> Returns a new DateTime instance.
     */
    public static function from (string $datetime, null|NamedTimezone|FixedTimezone $timezone = null):static {

        try {

            $datetime = new DateTimeImmutable(
                $datetime,
                $timezone?->native() ?? new NamedTimezone(Zone::UTC)->native()
            );

        } catch (DateMalformedStringException) {

            throw new InvalidDateTimeException;

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
     * @uses \FireHub\Foundation\Temporal\NamedTimezone::native() To get the native timezone.
     * @uses \FireHub\Core\Type\Date\Zone::UTC To get the UTC timezone.
     *
     * @param non-empty-string $datetime <p>
     * A date/time string.
     * </p>
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Format $format [optional] <p>
     * The format used to parse the datetime value.
     * </p>
     * @param null|TimezoneType $timezone [optional] <p>
     * The timezone used to parse the datetime value.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime value cannot be parsed
     * using the given format.
     *
     * @return static<non-empty-string> A new DateTime instance.
     */
    public static function fromFormat (string $datetime, string|Format $format = Format::ISO_DATE_TIME, null|NamedTimezone|FixedTimezone $timezone = null):static {

        $format = $format instanceof Format ? $format->value : $format;

        $datetime = DateTimeImmutable::createFromFormat(
            $format,
            $datetime,
            $timezone?->native() ?? new NamedTimezone(Zone::UTC)->native()
        ) ?: throw new InvalidDateTimeException('DateTime value could not be parsed using the format.');

        return new static($datetime);

    }

    /**
     * ### Creates a datetime value from a Unix timestamp
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $timestamp = DateTime::from('2000-01-01 12:00:00')->timestamp();
     *
     * $datetime = DateTime::fromTimestamp($timestamp);
     *
     * // '2000-01-01 12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\NamedTimezone::native() To get the native timezone.
     * @uses \FireHub\Core\Type\Date\Zone::UTC To get the UTC timezone.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To explode the timestamp string.
     *
     * @param \FireHub\Foundation\Temporal\UnixTimestamp<numeric-string> $timestamp <p>
     * The timestamp value.
     * </p>
     * @param null|TimezoneType $timezone [optional] <p>
     * The timezone used to parse the datetime value.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the timestamp value cannot be parsed
     * using the given format.
     *
     * @return static<non-empty-string> A new DateTime instance.
     */
    public static function fromTimestamp (UnixTimestamp $timestamp, null|NamedTimezone|FixedTimezone $timezone = null):static {

        [$seconds, $fraction] = Runtime\Str\SB\Delimiter::explode($timestamp->value(), '.', 2); // @phpstan-ignore-line

        $datetime = DateTimeImmutable::createFromFormat(
            'U.u',
            $seconds.'.'.$fraction
        ) ?: throw new InvalidDateTimeException('Timestamp value could not be converted to a datetime.');

        $datetime = $datetime->setTimezone(
            $timezone?->native() ?? new NamedTimezone(Zone::UTC)->native()
        );

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
     *
     * @return TimezoneType The timezone of the date and time value.
     *
     * @phpstan-ignore method.childReturnType
     */
    public function timezone ():NamedTimezone|FixedTimezone {

        return $this->timezone;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->timestamp()->value();
     *
     * // '946728000.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException If the timestamp is invalid.
     *
     * @return \FireHub\Foundation\Temporal\UnixTimestamp<numeric-string> The timestamp of the date and time value.
     *
     * @phpstan-ignore method.childReturnType
     */
    public function timestamp ():UnixTimestamp {

        return new UnixTimestamp($this->value->getTimestamp());

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
     * ### Formats the date and time value using the specified format
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Format;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->parse(Format::ATOM_EXTENDED);
     *
     * // '2000-01-01T12:00:00.000+00:00'
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->parse('H:i:s');
     *
     * // '12:00:00'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Token::value() To get the format token value.
     *
     * @param string|\FireHub\Core\Meta\Enum\Date\Format\Token $format <p>
     * The format to use for formatting the value.
     * </p>
     *
     * @return non-empty-string The formatted value.
     */
    public function format (string|Token $format):string {

        $format = $format instanceof Token
            ? $format->value()
            : $format;

        /** @var non-empty-string */
        return $this->value->format($format);

    }

    /**
     * ### Returns a new instance with the specified timezone
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
     * @param TimezoneType $timezone <p>
     * The timezone to set.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime string is invalid.
     *
     * @return static The new instance with provided timezone.
     */
    public function withTimezone (NamedTimezone|FixedTimezone $timezone):static {

        /** @var static<TValue> */
        return self::from($this->value(), $timezone);

    }

}