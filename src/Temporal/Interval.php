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
    Interval as BaseInterval, Period as BasePeriod, Timespan as BaseTimespan
};

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
 * @template TValue of array<array-key, mixed>
 *
 * @extends \FireHub\Core\Type\Temporal\Interval<TValue>
 *
 * @phpstan-consistent-constructor
 *
 * @todo finish afer implementation od DataStructures (methods: at, count, contains(DateTime), next, previous)
 */
readonly class Interval extends BaseInterval {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Temporal\Period<non-empty-string> $period <p>
     * The period of time that the interval spans.
     * </p>
     * @param \FireHub\Core\Type\Temporal\Timespan<numeric-string> $timespan <p>
     * The interval between successive occurrences.
     * </p>
     * @param bool $start_inclusive [optional] <p>
     * Whether the start of the interval is inclusive.
     * </p>
     * @param bool $end_inclusive [optional] <p>
     * Whether the end of the interval is inclusive.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected BasePeriod $period,
        protected BaseTimespan $timespan,
        protected bool $start_inclusive = true,
        protected bool $end_inclusive = true
    ) {}

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
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():array {

        /** @var TValue */
        return [];

    }

}