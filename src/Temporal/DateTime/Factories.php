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

namespace FireHub\Foundation\Temporal\DateTime;

use FireHub\Core\Type\Date\Zone;
use FireHub\Core\Meta\Enum\Date\Expression\ {
    DateKeyword, Keyword, Ordinal, Relative, TimeKeyword,
};
use FireHub\Core\Meta\Enum\Date\ {
    ExtendedUnit, Format, Month, Unit, WeekDay
};
use FireHub\Foundation\Temporal\ {
    NamedTimezone, FixedTimezone, UnixTimestamp
};
use FireHub\Foundation\Temporal\Exception\InvalidDateTimeException;
use FireHub\Runtime;
use DateMalformedStringException, DateTimeImmutable;

/**
 * ### Provides immutable DateTime factory methods
 *
 * The Factories trait provides static factory methods for creating immutable DateTime instances from strings,
 * timestamps, formatted values, and human-readable temporal expressions.
 *
 * It centralizes object creation while keeping the DateTime class focused on its core temporal behavior.
 * @since 1.0.0
 *
 * @phpstan-type TimezoneType \FireHub\Foundation\Temporal\NamedTimezone<value-of<\FireHub\Core\Type\Date\Zone>>|\FireHub\Foundation\Temporal\FixedTimezone<non-empty-string>
 */
trait Factories {

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
     * ### Creates a new DateTime instance with the current date and time
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::now()->value();
     *
     * // current date and time
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Keyword::NOW To get the current date and time.
     *
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
    public static function now (null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            Keyword::NOW->value,
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the current date
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Expression\TimeKeyword;
     *
     * $datetime = DateTime::today(TimeKeyword::NOON)->value();
     *
     * // today date and noon time
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\TimeKeyword::TODAY To get the current calendar day.
     *
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
    public static function today (string|TimeKeyword $at = TimeKeyword::MIDNIGHT, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            DateKeyword::TODAY->value.' '.($at instanceof TimeKeyword ? $at->value : $at),
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the yesterday date
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Expression\TimeKeyword;
     *
     * $datetime = DateTime::yesterday(TimeKeyword::NOON)->value();
     *
     * // yesterday date and noon time
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\TimeKeyword::YESTERDAY To get the yesterday calendar day.
     *
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
    public static function yesterday (string|TimeKeyword $at = TimeKeyword::MIDNIGHT, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            DateKeyword::YESTERDAY->value.' '.($at instanceof TimeKeyword ? $at->value : $at),
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the tomorrow date
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Expression\TimeKeyword;
     *
     * $datetime = DateTime::tomorrow(TimeKeyword::NOON)->value();
     *
     * // tomorrow date and noon time
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\TimeKeyword::TOMORROW To get the tomorrow calendar day.
     *
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
    public static function tomorrow (string|TimeKeyword $at = TimeKeyword::MIDNIGHT, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            DateKeyword::TOMORROW->value.' '.($at instanceof TimeKeyword ? $at->value : $at),
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the date and time relative to the current date and time
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Unit;
     *
     * $datetime = DateTime::relative(2, Unit::HOUR)->value();
     *
     * // current datetime with 2 hours added
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Keyword::NOW To get the current date and time.
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::factor() To get the factor of the unit.
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::base() To get the base unit of the unit.
     *
     * @param int $number <p>
     * The number of units to add to the current date and time.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Date\Unit|\FireHub\Core\Meta\Enum\Date\ExtendedUnit $unit <p>
     * The unit of time to add to the current date and time.
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
    public static function relative (int $number, Unit|ExtendedUnit $unit, null|NamedTimezone|FixedTimezone $timezone = null):static {

        if ($unit instanceof ExtendedUnit) {

            $number *= $unit->factor();
            $unit = $unit->base();

        }

        /** @var \FireHub\Core\Meta\Enum\Date\Unit $unit */
        return static::from(
            Keyword::NOW->value.' '.$number.' '.$unit->name,
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance at a specified temporal position
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Expression\Ordinal;
     * use FireHub\Core\Meta\Enum\Date\Unit;
     *
     * $datetime = DateTime::at(Ordinal::SECOND, Unit::MINUTE)->value();
     *
     * // current date with 2 seconds set
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     *
     * @param \FireHub\Core\Meta\Enum\Date\Expression\Relative|\FireHub\Core\Meta\Enum\Date\Expression\Ordinal $position <p>
     * The position of the date and time relative to the current date and time.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Date\Unit $unit <p>
     * The unit of time to add to the current date and time.
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
    public static function at (Relative|Ordinal $position, Unit $unit, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            $position->value.' '.$unit->name.' 00:00:00',
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the first day of the month
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Month;
     *
     * $datetime = DateTime::firstDayOfMonth(Month::NOVEMBER, 2000)->value();
     *
     * // first day of November 2000
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Ordinal::FIRST To get the first day of the month.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::longName() To get the long name of the month.
     *
     * @param \FireHub\Core\Meta\Enum\Date\Month $month <p>
     * The month of the year.
     * </p>
     * @param null|int<-9999,9999> $year [optional] <p>
     * The year of the month.
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
    public static function firstDayOfMonth (Month $month, ?int $year = null, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            Ordinal::FIRST->value.' day of '.$month->longName().' '.$year.' 00:00:00',
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the last day of the month
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Month;
     *
     * $datetime = DateTime::lastDayOfMonth(Month::NOVEMBER, 2000)->value();
     *
     * // last day of November 2000
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Relative::LAST To get the last day of the month.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::longName() To get the long name of the month.
     *
     * @param \FireHub\Core\Meta\Enum\Date\Month $month <p>
     * The month of the year.
     * </p>
     * @param null|int<-9999,9999> $year [optional] <p>
     * The year of the month.
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
    public static function lastDayOfMonth (Month $month, ?int $year = null, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            Relative::LAST->value.' day of '.$month->longName().' '.$year.' 00:00:00',
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the first day of a relative month
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Expression\Relative;
     *
     * $datetime = DateTime::firstDayOfRelativeMonth(Relative::NEXT)->value();
     *
     * // first day of the current year
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Ordinal::FIRST To get the first day of the month.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::longName() To get the long name of the month.
     *
     * @param \FireHub\Core\Meta\Enum\Date\Expression\Relative $month <p>
     * The relative month.
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
    public static function firstDayOfRelativeMonth (Relative $month, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            Ordinal::FIRST->value.' day of '.$month->value.' month 00:00:00',
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the last day of a relative month
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Expression\Relative;
     *
     * $datetime = DateTime::lastDayOfRelativeMonth(Relative::NEXT)->value();
     *
     * // last day of the current year
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Relative::LAST To get the last day of the month.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::longName() To get the long name of the month.
     *
     * @param \FireHub\Core\Meta\Enum\Date\Expression\Relative $month <p>
     * The relative month.
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
    public static function lastDayOfRelativeMonth (Relative $month, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            Relative::LAST->value.' day of '.$month->value.' month 00:00:00',
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the weekday of the month
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Expression\Ordinal;
     * use FireHub\Core\Meta\Enum\Date\Expression\Relative;
     * use FireHub\Core\Meta\Enum\Date\Month;
     * use FireHub\Core\Meta\Enum\Date\WeekDay;
     *
     * $datetime = DateTime::weekDayOfMonth(Ordinal::FIRST, WeekDay::SATURDAY, Month::JULY, 2000)->value();
     *
     * // first saturday of July 2000
     *
     * $datetime = DateTime::weekDayOfMonth(Relative::NEXT, WeekDay::SATURDAY, Month::JULY, 2000)->value();
     *
     * // next saturday of July 2000
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Ordinal::FIRST To get the first weekday of the month.
     * @uses \FireHub\Core\Meta\Enum\Date\WeekDay::shortName() To get the short name of the weekday.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::longName() To get the long name of the month.
     *
     * @param \FireHub\Core\Meta\Enum\Date\Expression\Relative|\FireHub\Core\Meta\Enum\Date\Expression\Ordinal $position <p>
     * The position of the weekday in the month.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Date\WeekDay $weekday <p>
     * The weekday of the month.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Date\Month $month <p>
     * The month of the year.
     * </p>
     * @param null|int<-9999,9999> $year [optional] <p>
     * The year of the month.
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
    public static function weekDayOfMonth (Relative|Ordinal $position, WeekDay $weekday, Month $month, ?int $year = null, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            $position->value.' '.$weekday->shortName().' of '.$month->longName().' '.$year.' 00:00:00',
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with the weekday of the relative month
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\Expression\Ordinal;
     * use FireHub\Core\Meta\Enum\Date\Expression\Relative;
     * use FireHub\Core\Meta\Enum\Date\WeekDay;
     *
     * $datetime = DateTime::weekdayOfRelativeMonth(Ordinal::FIRST, WeekDay::SATURDAY, Relative::NEXT)->value();
     *
     * // first saturday of July 2000
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Ordinal::FIRST To get the first weekday of the month.
     * @uses \FireHub\Core\Meta\Enum\Date\WeekDay::shortName() To get the short name of the weekday.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::longName() To get the long name of the month.
     *
     * @param \FireHub\Core\Meta\Enum\Date\Expression\Ordinal $ordinal <p>
     * The ordinal of the weekday.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Date\WeekDay $weekday <p>
     * The weekday of the month.
     * </p>
     * @param \FireHub\Core\Meta\Enum\Date\Expression\Relative $month <p>
     * The relative month.
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
    public static function weekdayOfRelativeMonth (Ordinal $ordinal, WeekDay $weekday, Relative $month, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            $ordinal->value.' '.$weekday->shortName().' of '.$month->value.' month 00:00:00',
            $timezone
        );

    }

    /**
     * ### Creates a new DateTime instance with next weekday
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\WeekDay;
     *
     * $datetime = DateTime::nextWeekDay(WeekDay::SATURDAY)->value();
     *
     * // next saturday
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::longName() To get the long name of the month.
     *
     * @param \FireHub\Core\Meta\Enum\Date\WeekDay $weekday <p>
     * The weekday of the month.
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
    public static function nextWeekDay (WeekDay $weekday, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            $weekday->longName(),
            $timezone
        );

    }

    /**
     * ### Creates a DateTime instance at a relative weekday
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Core\Meta\Enum\Date\WeekDay;
     *
     * $datetime = DateTime::weekDayOf(WeekDay::FRIDAY, Relative::THIS)->value();
     *
     * // this friday
     * </code>
     *
     * @since 1.0.0
     *
     * @uses static::from() To create a new DateTime instance.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::longName() To get the long name of the month.
     *
     * @param \FireHub\Core\Meta\Enum\Date\WeekDay $weekday <p>
     * The weekday of the month.
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
    public static function weekDayOf (WeekDay $weekday, Relative $relative, null|NamedTimezone|FixedTimezone $timezone = null):static {

        return static::from(
            $weekday->longName().' '.$relative->value.' week',
            $timezone
        );

    }

}