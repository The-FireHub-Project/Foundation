<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\Temporal\DateTime;

/**
 * ### Provides immutable date and time mutators
 *
 * The Mutators trait provides methods for creating new DateTime instances with modified temporal values while
 * preserving the immutability of the original instance.
 *
 * It centralizes value transformation operations while keeping the DateTime class focused on its core temporal
 * behavior.
 * @since 1.0.0
 */
trait Mutators {

    /**
     * ### Set the date of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withDate(2026, 1, 1)->value();
     *
     * // '2026-01-01 12:00:00.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @param int<-9999,9999> $year <p>
     * The year of the date.
     * </p>
     * @param int<1,12> $month <p>
     * The month of the date.
     * </p>
     * @param int<1,31> $day <p>
     * The day of the date.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified date.
     */
    public function withDate (int $year, int $month, int $day):static {

        return new static($this->value->setDate($year, $month, $day));

    }

    /**
     * ### Set the time of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withTime(13, 1, 1, 111111)->value();
     *
     * // '2000-01 13:01:01.111111'
     * </code>
     *
     * @since 1.0.0
     *
     * @param int<0,23> $hour <p>
     * The hour of the time.
     * </p>
     * @param int<0,59> $minute <p>
     * The minute of the time.
     * </p>
     * @param int<0,59> $second <p>
     * The second of the time.
     * </p>
     * @param int<0,999999> $microsecond <p>
     * The microsecond of the time.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified time.
     */
    public function withTime (int $hour, int $minute, int $second, int $microsecond):static {

        return new static($this->value->setTime($hour, $minute, $second, $microsecond));

    }

    /**
     * ### Set the year of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withYear(2026)->value();
     *
     * // '2026-01-01 12:00:00.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withDate() To set the date of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::month() To get the month of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::dayOfMonth() To get the day of the DateTime value.
     *
     * @param int<-9999,9999> $year <p>
     * The year of the date.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified year.
     */
    public function withYear (int $year):static {

        return $this->withDate($year, $this->month()->value, $this->dayOfMonth());

    }

    /**
     * ### Set the month of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withMonth(6)->value();
     *
     * // '2000-06-01 12:00:00.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withDate() To set the date of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::year() To get the year of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::dayOfMonth() To get the day of the DateTime value.
     *
     * @param int<1,12> $month <p>
     * The month of the date.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified month.
     */
    public function withMonth (int $month):static {

        return $this->withDate($this->year(), $month, $this->dayOfMonth());

    }

    /**
     * ### Set the day of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withDay(15)->value();
     *
     * // '2000-01-15 12:00:00.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withDate() To set the date of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::year() To get the year of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::month() To get the month of the DateTime value.
     *
     * @param int<1,31> $day <p>
     * The day of the date.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified day.
     */
    public function withDay (int $day):static {

        return $this->withDate($this->year(), $this->month()->value, $day);

    }

    /**
     * ### Set the hour of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withHour(13)->value();
     *
     * // '2000-01-01 13:00:00.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withTime() To set the time of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::minute() To get the minute of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::second() To get the second of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::microsecond() To get the microsecond of the DateTime value.
     *
     * @param int<0,23> $hour <p>
     * The hour of the time.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified hour.
     */
    public function withHour (int $hour):static {

        return $this->withTime(
            $hour,
            $this->minute(),
            $this->second(),
            $this->microsecond()
        );

    }

    /**
     * ### Set the minute of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withMinute(30)->value();
     *
     * // '2000-01-01 12:30:00.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withTime() To set the time of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::hour() To get the hour of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::second() To get the second of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::microsecond() To get the microsecond of the DateTime value.
     *
     * @param int<0,59> $minute <p>
     * The minute of the time.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified minute.
     */
    public function withMinute (int $minute):static {

        return $this->withTime(
            $this->hour(),
            $minute,
            $this->second(),
            $this->microsecond()
        );

    }

    /**
     * ### Set the second of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withSecond(30)->value();
     *
     * // '2000-01-01 12:00:30.123456'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withTime() To set the time of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::hour() To get the hour of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::minute() To get the minute of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::microsecond() To get the microsecond of the DateTime value.
     *
     * @param int<0,59> $second <p>
     * The second of the time.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified second.
     */
    public function withSecond (int $second):static {

        return $this->withTime(
            $this->hour(),
            $this->minute(),
            $second,
            $this->microsecond()
        );

    }

    /**
     * ### Set the microsecond of the DateTime value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00.123456')->withMicrosecond(654321)->value();
     *
     * // '2000-01-01 12:00:00.654321'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::withTime() To set the time of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::hour() To get the hour of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::minute() To get the minute of the DateTime value.
     * @uses \FireHub\Foundation\Temporal\DateTime::second() To get the second of the DateTime value.
     *
     * @param int<0,999999> $microsecond <p>
     * The microsecond of the time.
     * </p>
     *
     * @return static<non-empty-string> Returns a new DateTime instance with the modified microsecond.
     */
    public function withMicrosecond (int $microsecond):static {

        return $this->withTime(
            $this->hour(),
            $this->minute(),
            $this->second(),
            $microsecond
        );

    }

}