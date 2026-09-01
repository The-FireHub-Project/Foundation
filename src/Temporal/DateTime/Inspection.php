<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Foundation
 */

namespace FireHub\Foundation\Temporal\DateTime;

use FireHub\Core\Meta\Enum\Date\ {
    Format, Month, WeekDay
};
use FireHub\Core\Meta\Enum\Date\Format\ {
    Year, Timezone
};
use FireHub\Core\Meta\Enum\Date\Expression\Relative;
use FireHub\Foundation\Temporal\DateTime;

/**
 * ### Provides date and time state inspections
 *
 * The Inspection trait provides methods for determining properties and states of a DateTime value, such as leap
 * years, daylight saving time, weekends, and timezone characteristics.
 *
 * It centralizes temporal state inspection operations while keeping the DateTime class focused on its core temporal
 * behavior.
 * @since 1.0.0
 */
trait Inspection {

    /**
     * ### Check if the date and time value is in a leap year
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isLeapYear();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To check if the date and time value is in a leap year.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Year::LEAP_YEAR To check if the date and time value is in a leap year.
     *
     * @return bool Returns true if the date and time value is in a leap year, false otherwise.
     */
    public function isLeapYear ():bool {

        return (bool)$this->format(Year::LEAP_YEAR);

    }

    /**
     * ### Check if the date and time value has daylight saving time
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isDST();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To check if the date and time value has daylight saving
     * time.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Timezone::DAYLIGHT_SAVING To check if the date and time value has
     * daylight saving time.
     *
     * @return bool Returns true if the date and time value has daylight saving time, false otherwise.
     */
    public function isDST ():bool {

        return (bool)$this->format(Timezone::DAYLIGHT_SAVING);

    }

    /**
     * ### Check if the date and time value is today
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isToday();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To check if the date and time value is today.
     * @uses \FireHub\Core\Meta\Enum\Date\Format::ISO_DATE To check if the date and time value is today.
     *
     * @return bool Returns true if the date and time value is today, false otherwise.
     */
    public function isToday ():bool {

        return $this->format(Format::ISO_DATE) === static::today(timezone: $this->timezone)->format(Format::ISO_DATE);

    }

    /**
     * ### Check if the date and time value is yesterday
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isYesterday();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To check if the date and time value is yesterday.
     * @uses \FireHub\Core\Meta\Enum\Date\Format::ISO_DATE To check if the date and time value is yesterday.
     *
     * @return bool Returns true if the date and time value is yesterday, false otherwise.
     */
    public function isYesterday ():bool {

        return $this->format(Format::ISO_DATE) === static::yesterday(timezone: $this->timezone)->format(Format::ISO_DATE);

    }

    /**
     * ### Check if the date and time value is tomorrow
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isTomorrow();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To check if the date and time value is tomorrow.
     * @uses \FireHub\Core\Meta\Enum\Date\Format::ISO_DATE To check if the date and time value is tomorrow.
     *
     * @return bool Returns true if the date and time value is tomorrow, false otherwise.
     */
    public function isTomorrow ():bool {

        return $this->format(Format::ISO_DATE) === static::tomorrow(timezone: $this->timezone)->format(Format::ISO_DATE);

    }

    /**
     * ### Check if the date and time value is the first day of the month
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isFirstDayOfMonth();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::dayOfMonth() To get the day of the month.
     *
     * @return bool Returns true if the date and time value is first day of the month, false otherwise.
     */
    public function isFirstDayOfMonth ():bool {

        return $this->dayOfMonth() === 1;

    }

    /**
     * ### Check if the date and time value is the last day of the month
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isLastDayOfMonth();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::dayOfMonth() To get the day of the month.
     * @uses \FireHub\Foundation\Temporal\DateTime\Factories::lastDayOfRelativeMonth() To get the last day of the
     * relative month.
     * @uses \FireHub\Core\Meta\Enum\Date\Expression\Relative::THIS To get the last day of the month.
     *
     * @return bool Returns true if the date and time value is last day of the month, false otherwise.
     */
    public function isLastDayOfMonth ():bool {

        return $this->dayOfMonth() === static::lastDayOfRelativeMonth(Relative::THIS, $this->timezone)->dayOfMonth();

    }

    /**
     * ### Check if the date and time value is the first day of the year
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isFirstDayOfYear();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::dayOfYear() To get the day of the year.
     *
     * @return bool Returns true if the date and time value is first day of the year, false otherwise.
     */
    public function isFirstDayOfYear ():bool {

        return $this->dayOfYear() === 1;

    }

    /**
     * ### Check if the date and time value is the last day of the year
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isLastDayOfYear();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::dayOfYear() To get the day of the year.
     * @uses \FireHub\Foundation\Temporal\DateTime\Factories::lastDayOfMonth() To get the last day of the year.
     * @uses \FireHub\Core\Meta\Enum\Date\Month::DECEMBER As month of the year.
     *
     * @return bool Returns true if the date and time value is last day of the year, false otherwise.
     */
    public function isLastDayOfYear ():bool {

        return $this->dayOfYear() === static::lastDayOfMonth(Month::DECEMBER, timezone: $this->timezone)->dayOfYear();

    }

    /**
     * ### Check if the date and time value it in a weekend
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isWeekend();
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::weekDay() To get the week day.
     * @uses \FireHub\Core\Meta\Enum\Date\WeekDay::SATURDAY To check if the date and time value it in a weekend.
     * @uses \FireHub\Core\Meta\Enum\Date\WeekDay::SUNDAY To check if the date and time value it in a weekend.
     *
     * @return bool Returns true if the date and date and time value it in a weekend, false otherwise.
     */
    public function isWeekend ():bool {

        return $this->weekDay() === WeekDay::SATURDAY || $this->weekDay() === WeekDay::SUNDAY;

    }

    /**
     * ### Check if the date and time value is a weekday
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->isWeekday();
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime\Inspection::isWeekend() To check if the date and time value it in a
     * weekend.
     *
     * @return bool Returns true if the date and date and time value is a weekday, false otherwise.
     */
    public function isWeekday ():bool {

        return !$this->isWeekend();

    }

    /**
     * ### Determines whether this date and time is equal to another
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.000000');
     * $datetime2 = DateTime::from('2000-01-01 12:00:00.000001');
     *
     * $comparison = $datetime->isEqual($datetime2);
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @param self<non-empty-string> $datetime $datetime <p>
     * The date and time to compare against.
     * </p>
     *
     * @return bool True if both date and time values represent the same instant.
     */
    public function isEqual (self $datetime):bool {

        return $this->comparisonValue() === $datetime->comparisonValue();

    }

    /**
     * ### Determines whether this date and time is before another date and time
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.000000');
     * $datetime2 = DateTime::from('2000-01-01 12:00:00.000001');
     *
     * $comparison = $datetime->isBefore($datetime2);
     *
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @param self<non-empty-string> $datetime <p>
     * The date and time to compare against.
     * </p>
     *
     * @return bool Returns true if this date and time is before the given date and time.
     */
    public function isBefore (self $datetime):bool {

        return $this->comparisonValue() < $datetime->comparisonValue();

    }

    /**
     * ### Determines whether this date and time is after another date and time
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.000000');
     * $datetime2 = DateTime::from('2000-01-01 12:00:00.000001');
     *
     * $comparison = $datetime->isAfter($datetime2);
     *
     * // false
     * </code>
     *
     * @since 1.0.0
     *
     * @param self<non-empty-string> $datetime <p>
     * The date and time to compare against.
     * </p>
     *
     * @return bool Returns true if this date and time is after the given date and time.
     */
    public function isAfter (self $datetime):bool {

        return $this->comparisonValue() > $datetime->comparisonValue();

    }

}