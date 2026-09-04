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

use FireHub\Core\Type\Temporal\ {
    DateTime as BaseDateTime, Period as BasePeriod, Timespan as BaseTimespan
};
use FireHub\Foundation\Temporal\Exception\InvalidPeriodException;

/**
 * ### Provides an immutable temporal Period Value Object with a high-level developer API
 *
 * The Period class represents a sequence of recurring temporal occurrences within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for creating, inspecting, and iterating over temporal
 * occurrences while preserving immutable value semantics inherited from the Core Value Object system.
 *
 * The class is responsible for high-level period operations and developer experience, while low-level temporal
 * functionality remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\Temporal\Period<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Period extends BasePeriod {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Foundation\Temporal\DateTime::isBefore() To check if the start date and time is before the end
     * date and time.
     *
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $start <p>
     * The start date and time of the period.
     * </p>
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $end <p>
     * The end date and time of the period.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException If the start date and time is after the
     * end date and time.
     *
     * @return void
     */
    public function __construct (
        protected DateTime $start,
        protected DateTime $end
    ) {

        $this->guard(
            fn ():bool => $start->isBefore($end),
            fn ():InvalidPeriodException => new InvalidPeriodException(
                'The start date and time cannot be after the end date and time.'
            )
        );

    }

    /**
     * ### Checks if a given DateTime object is within the period
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $datetime = new Period($datetime, $datetime2)->value();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Temporal\DateTime<non-empty-string> $datetime <p>
     * The DateTime object to check.
     * </p>
     *
     * @return bool Returns true if the given DateTime object is within the period, false otherwise.
     */
    public function inPeriod (BaseDateTime $datetime):bool {

        return (($datetime >= $this->start) && ($datetime <= $this->end));

    }

    /**
     * ### Checks if a given DateTime object is in progress
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $datetime = new Period($datetime, $datetime2)->value();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateTimeException If the datetime string is invalid.
     *
     * @return bool Returns true if the given DateTime object is in progress, false otherwise.
     */
    public function inProgress ():bool {

        return $this->inPeriod(DateTime::now());

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.000000');
     * $datetime2 = new DateTime('2001-01-01 12:00:00.000000');
     *
     * $datetime = new Period($datetime, $datetime2)->duration()->components()->value();
     *
     * // ['days' => '366', 'hours' => 0, 'minutes' => 0, 'seconds' => 0, 'microseconds' => 0]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Temporal\Timespan::diff() To get the duration of the period.
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks If the ticks value is not numeric.
     *
     * @return \FireHub\Foundation\Temporal\Timespan<numeric-string> The duration of the period as a Timespan Value
     * Object.
     */
    public function duration ():Timespan {

        return $this->end->diff($this->start);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $datetime = new Period($datetime, $datetime2)->value();
     *
     * // '2000-01-01 12:00:00.111111 - 2021-05-01 18:30:00.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::value() To get the date and time value.
     *
     * @return non-empty-string The date and time value of the period.
     */
    public function value ():string {

        return $this->start->value() . ' - ' . $this->end->value();

    }

    /**
     * ### Reduces the start date and time by a given timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.000000');
     * $datetime2 = new DateTime('2001-01-01 12:00:00.000000');
     * $timespan = new Timespan('0')->addDays('1')->addHours('12')->addMicroseconds('123456');
     *
     * $datetime = new Period($datetime, $datetime2)->reduceStart($timespan);
     *
     * // '1999-12-30 23:59:59.876544 - 2021-01-01 12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::sub() To reduce the start date and time by a given timespan.
     *
     * @param \FireHub\Core\Type\Temporal\Timespan<numeric-string> $timespan <p>
     * The timespan to reduce the start date and time by.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException If the start date and time is after the
     * end date and time.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents If the components are invalid.
     *
     * @return static<non-empty-string> A new Period object with the start date and time reduced by the given timespan.
     */
    public function reduceStart (BaseTimespan $timespan):static {

        return new static($this->start->sub($timespan), $this->end);

    }

    /**
     * ### Extends the start date and time by a given timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.000000');
     * $datetime2 = new DateTime('2001-01-01 12:00:00.000000');
     * $timespan = new Timespan('0')->addDays('1')->addHours('12')->addMicroseconds('123456');
     *
     * $datetime = new Period($datetime, $datetime2)->extendStart($timespan);
     *
     * // '2000-01-03 00:00:00.123456 - 2021-01-01 12:00:00.000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::add() To extend the start date and time by a given timespan.
     *
     * @param \FireHub\Core\Type\Temporal\Timespan<numeric-string> $timespan <p>
     * The timespan to extend the start date and time by.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException If the start date and time is after the
     * end date and time.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents If the components are invalid.
     *
     * @return static<non-empty-string> A new Period object with the start date and time extended by the given timespan.
     */
    public function extendStart (BaseTimespan $timespan):static {

        return new static($this->start->add($timespan), $this->end);

    }

    /**
     * ### Reduces the end date and time by a given timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.000000');
     * $datetime2 = new DateTime('2001-01-01 12:00:00.000000');
     * $timespan = new Timespan('0')->addDays('1')->addHours('12')->addMicroseconds('123456');
     *
     * $datetime = new Period($datetime, $datetime2)->reduceEnd($timespan);
     *
     * // '2000-01-01 12:00:00.000000 - 2020-12-30 23:59:59.876544'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::sub() To reduce the end date and time by a given timespan.
     *
     * @param \FireHub\Core\Type\Temporal\Timespan<numeric-string> $timespan <p>
     * The timespan to reduce the end date and time by.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException If the start date and time is after the
     * end date and time.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents If the components are invalid.
     *
     * @return static<non-empty-string> A new Period object with the end date and time reduced by the given timespan.
     */
    public function reduceEnd (BaseTimespan $timespan):static {

        return new static($this->start, $this->end->sub($timespan));

    }

    /**
     * ### Extends the end date and time by a given timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.000000');
     * $datetime2 = new DateTime('2001-01-01 12:00:00.000000');
     * $timespan = new Timespan('0')->addDays('1')->addHours('12')->addMicroseconds('123456');
     *
     * $datetime = new Period($datetime, $datetime2)->extendEnd($timespan);
     *
     * // '2000-01-01 12:00:00.000000 - 2021-01-03 00:00:00.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::add() To extend the end date and time by a given timespan.
     *
     * @param \FireHub\Core\Type\Temporal\Timespan<numeric-string> $timespan <p>
     * The timespan to extend the end date and time by.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidPeriodException If the start date and time is after the
     * end date and time.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents If the components are invalid.
     *
     * @return static<non-empty-string> A new Period object with the end date and time extended by the given timespan.
     */
    public function extendEnd (BaseTimespan $timespan):static {

        return new static($this->start, $this->end->add($timespan));

    }

}