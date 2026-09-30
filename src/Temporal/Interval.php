<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\Temporal;

use FireHub\Core\Type\Temporal\Interval as BaseInterval;
use FireHub\Core\Type\Maybe;
use FireHub\Foundation\DataStructure\DS;
use FireHub\Foundation\DataStructure\Stream;
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Foundation\Temporal\Exception\InvalidIntervalTimespanException;
use FireHub\Runtime;
use Traversable;

/**
 * ### Provides an immutable temporal Interval Value Object with a high-level developer API
 *
 * The Interval class represents a sequence of recurring temporal occurrences within a bounded period of time.
 *
 * It combines a Period defining the temporal boundaries with a Timespan defining the interval between successive
 * occurrences.
 *
 * It provides an expressive and object-oriented interface for creating, inspecting, and iterating over temporal
 * occurrences while preserving immutable value semantics inherited from the Core Value Object system.
 *
 * The class is responsible for high-level interval operations and developer experience, while low-level temporal
 * functionality remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of array{
 *     period: non-empty-string,
 *     timespan: numeric-string,
 *     start_inclusive: bool,
 *     end_inclusive: bool
 * }
 *
 * @extends \FireHub\Core\Type\Temporal\Interval<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Interval extends BaseInterval {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Temporal\Period<non-empty-string> $period <p>
     * The period of time that the interval spans.
     * </p>
     * @param \FireHub\Foundation\Temporal\Timespan<numeric-string> $timespan <p>
     * The interval between successive occurrences.
     * </p>
     * @param bool $start_inclusive [optional] <p>
     * Whether the start of the interval is inclusive.
     * </p>
     * @param bool $end_inclusive [optional] <p>
     * Whether the end of the interval is inclusive.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidIntervalTimespanException If the timespan is not greater
     * than zero.
     *
     * @return void
     */
    public function __construct (
        protected Period $period,
        protected Timespan $timespan,
        protected bool $start_inclusive = true,
        protected bool $end_inclusive = true
    ) {

        $this->guard(
            fn ():bool => $timespan->value()[0] !== '-' && $timespan->value() !== '0',
            fn () => new InvalidIntervalTimespanException(
                'Interval timespan must be greater than zero.'
            )
        );

    }

    /**
     * ### Determines whether the start boundary is inclusive
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, true)->isStartInclusive();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the start boundary is included, false otherwise.
     */
    public function isStartInclusive ():bool {

        return $this->start_inclusive;

    }

    /**
     * ### Determines whether the end boundary is inclusive
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, false, true)->isStartInclusive();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the end boundary is included, false otherwise.
     */
    public function isEndInclusive ():bool {

        return $this->end_inclusive;

    }

    /**
     * ### Determines whether the interval contains no occurrences
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan)->isEmpty();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Interval::count() To get the number of occurrences.
     *
     * @return bool True if the interval contains no occurrences, false otherwise.
     */
    public function isEmpty ():bool {

        return $this->count() === 0;

    }

    /**
     * ### Counts interval occurrences
     *
     * Calculates the number of recurring occurrences contained within the interval according to its period,
     * timespan, and boundary inclusivity.
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan)->count();
     *
     * // 7792
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Period::duration() To get the duration of the interval period.
     * @uses \FireHub\Foundation\Temporal\Timespan::microseconds() To get timespan values in microseconds.
     * @uses \FireHub\Runtime\Math\DecimalEngine::divide() To divide the period duration by the interval timespan.
     * @uses \FireHub\Runtime\Math\DecimalEngine::mod() To determine whether the end boundary is an occurrence.
     * @uses \FireHub\Runtime\Math::max() To ensure the count is non-negative.
     *
     * @return non-negative-int The number of occurrences.
     */
    public function count ():int {

        $step = $this->timespan->value();
        $duration = $this->period->duration()->value();

        $count = (int)Runtime\Math\DecimalEngine::divide($duration, $step, 0) + 1;

        if (!$this->start_inclusive)
            $count--;

        if (
            !$this->end_inclusive
            && Runtime\Math\DecimalEngine::mod($duration, $step) === '0'
        ) $count--;

        /** @var non-negative-int */
        return Runtime\Math::max(0, $count);


    }

    /**
     * ### Gets the first occurrence
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, false, true)->first();
     *
     * // DateTime(2000-01-01 12:00:00.111111')
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\Maybe<\FireHub\Foundation\Temporal\DateTime<non-empty-string>> The first occurrence,
     * or none if the interval is empty.
     */
    public function first ():Maybe {

        return $this->at(0);

    }

    /**
     * ### Gets the last occurrence
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, false, true)->last();
     *
     * // DateTime(2021-05-01 12:00:00.111111')
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\Maybe<\FireHub\Foundation\Temporal\DateTime<non-empty-string>> The last occurrence,
     * or none if the interval is empty.
     */
    public function last ():Maybe {

        $count = $this->count();

        if ($count === 0)
            return new None;

        return $this->at($count - 1);

    }

    /**
     * ### Gets an occurrence by index
     *
     * Returns the occurrence located at the specified zero-based interval index.
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, false, true)->at(1);
     *
     * // DateTime('2000-01-02 12:00:00.111111')
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Interval::count() To get the number of occurrences.
     * @uses \FireHub\Runtime\Math\DecimalEngine::multiply() To multiply the interval timespan by the offset.
     *
     * @param non-negative-int $index <p>
     * Zero-based occurrence index.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<\FireHub\Foundation\Temporal\DateTime<non-empty-string>> The occurrence at the
     * specified index, or none if the index is outside the interval.
     */
    public function at (int $index):Maybe {

        if ($index < 0)
            return new None;

        $count = $this->count();

        if ($index >= $count)
            return new None;

        $offset = $this->start_inclusive
            ? $index
            : $index + 1;

        $ticks = Runtime\Math\DecimalEngine::multiply(
            $this->timespan->value(),
            (string)$offset
        );

        return new Some(
            $this->period->start()->add(
                new Timespan($ticks)
            )
        );

    }

    /**
     * ### Determines whether date and time are an interval occurrence
     *
     * Determines whether the provided date and time represent one of the recurring occurrences of this interval.
     *
     * A date and time may be inside the underlying period without being an occurrence of the interval.
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     * $datetime3 = new DateTime('2000-05-01 12:00:00.111111');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, false, true)->contains($datetime3);
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Period::inPeriod() To check if the date and time is within the period.
     * @uses \FireHub\Foundation\Temporal\Period::start() To get the start of the period.
     * @uses \FireHub\Foundation\Temporal\Period::end() To get the end of the period.
     * @uses \FireHub\Foundation\Temporal\DateTime::diff() To calculate the difference between the date and time and the
     * period start.
     * @uses \FireHub\Runtime\Math\DecimalEngine::mod() To determine whether the difference is an exact multiple of the
     * interval timespan.
     *
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $datetime <p>
     * The date and time to check.
     * </p>
     *
     * @return bool True if the date and time is an interval occurrence, false otherwise.
     */
    public function contains (DateTime $datetime):bool {

        if (!$this->period->inPeriod($datetime))
            return false;

        if (!$this->start_inclusive && $datetime === $this->period->start())
            return false;

        if (!$this->end_inclusive && $datetime === $this->period->end())
            return false;

        $distance = $datetime->diff($this->period->start())->value();

        return Runtime\Math\DecimalEngine::mod(
                $distance,
                $this->timespan->value()
            ) === '0';

    }

    /**
     * ### Gets the next occurrence
     *
     * Finds the first interval occurrence strictly after the provided date and time.
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     * $datetime3 = new DateTime('2000-05-01 12:00:00.111111');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, false, true)->next($datetime3);
     *
     * // DateTime('2000-05-02 12:00:00.111111')
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Interval::occurrences() To get all interval occurrences.
     *
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $datetime <p>
     * The reference date and time.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<\FireHub\Foundation\Temporal\DateTime<non-empty-string>> The next occurrence,
     * or none if no later occurrence exists.
     */
    public function next (DateTime $datetime):Maybe {

        foreach ($this->occurrences() as $occurrence)
            if ($occurrence > $datetime)
                return new Some($occurrence);

        return new None;

    }

    /**
     * ### Gets the previous occurrence
     *
     * Finds the last interval occurrence strictly before the provided date and time.
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     * $datetime3 = new DateTime('2000-05-01 12:00:00.111111');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, false, true)->previous($datetime3);
     *
     * // DateTime('2000-04-30 12:00:00.111111')
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $datetime <p>
     * The reference date and time.
     * </p>
     *
     * @return \FireHub\Core\Type\Maybe<\FireHub\Foundation\Temporal\DateTime<non-empty-string>> The previous
     * occurrence, or none if no earlier occurrence exists.
     */
    public function previous (DateTime $datetime):Maybe {

        $previous = null;

        foreach ($this->occurrences() as $occurrence) {

            if ($occurrence >= $datetime)
                break;

            $previous = $occurrence;

        }

        return $previous !== null
            ? new Some($previous)
            : new None;

    }

    /**
     * ### Creates a stream of interval occurrences
     *
     * Creates a lazy stream that yields all recurring occurrences within the interval.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Interval::occurrences() To get the interval occurrences.
     *
     * @return \FireHub\Foundation\DataStructure\Stream<int, \FireHub\Foundation\Temporal\DateTime<non-empty-string>> A
     * lazy stream of interval occurrences.
     */
    public function stream ():Stream {

        return DS::stream()->factory(
            fn ():iterable => $this->occurrences()
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Interval;
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new DateTime('2000-01-01 12:00:00.111111');
     * $datetime2 = new DateTime('2021-05-01 18:30:00.123456');
     *
     * $period = new Period($datetime, $datetime2);
     * $timespan = new Timespan('0')->addDays('1');
     *
     * $interval = new Interval($period, $timespan, false, true)->value();
     *
     * // [
     * //     'period' => '2000-01-01 12:00:00.111111 - 2021-05-01 18:30:00.123456',
     * //     'timespan' => '86400000000',
     * //     'start_inclusive' => true,
     * //     'end_inclusive' => true
     * // ]
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Period::value() To get the period value.
     * @uses \FireHub\Foundation\Temporal\Timespan::value() To get the timespan value.
     */
    public function value ():array {

        /** @var TValue */
        return [
            'period' => $this->period->value(),
            'timespan' => $this->timespan->value(),
            'start_inclusive' => $this->start_inclusive,
            'end_inclusive' => $this->end_inclusive
        ];

    }

    /**
     * ### Iterates over interval occurrences
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Interval::occurrences() To get the interval occurrences.
     *
     * @return Traversable<int, \FireHub\Foundation\Temporal\DateTime<non-empty-string>> Interval occurrences.
     */
    public function getIterator ():Traversable {

        yield from $this->occurrences();

    }


    /**
     * ### Iterates over interval occurrences
     *
     * Generates recurring interval occurrences in chronological order.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\Period::start() To get the start of the interval period.
     * @uses \FireHub\Foundation\Temporal\Timespan::add() To add the interval timespan to the start of the period.
     * @uses \FireHub\Runtime\Math\DecimalEngine::multiply() To multiply the interval timespan by the offset.
     *
     * @return iterable<int, \FireHub\Foundation\Temporal\DateTime<non-empty-string>> Interval occurrences.
     */
    private function occurrences ():iterable {

        $count = $this->count();

        for ($index = 0; $index < $count; $index++) {

            $offset = $this->start_inclusive
                ? $index
                : $index + 1;

            $ticks = Runtime\Math\DecimalEngine::multiply(
                $this->timespan->value(),
                (string)$offset
            );

            yield $index => $this->period->start()->add(
                new Timespan($ticks)
            );

        }

    }


}