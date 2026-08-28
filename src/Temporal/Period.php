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
    DateTime as BaseDateTime, Period as BasePeriod
};

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
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $start <p>
     * The start date and time of the period.
     * </p>
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $end <p>
     * The end date and time of the period.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected DateTime $start,
        protected DateTime $end
    ) {}

    /**
     * ### Checks if a given DateTime object is within the period
     *
     * <code>
     * use FireHub\Foundation\Temporal\Period;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = new Period(
     *     new DateTime('2000-01-01 12:00:00.111111'),
     *     new DateTime('2021-05-01 18:30:00.123456')
     * )->inPeriod(new DateTime('2000-01-01 12:00:00.111111'));
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
     * $datetime = new Period(
     *     new DateTime('2000-01-01 12:00:00.111111'),
     *     new DateTime('2021-05-01 18:30:00.123456')
     * )->inProgress();
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
     * $datetime = new Period(
     *     new DateTime('2000-01-01 12:00:00.111111'),
     *     new DateTime('2021-05-01 18:30:00.123456')
     * )->value();
     *
     * // '2000-01-01 12:00:00.000000'
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

}