<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.0
 * @package Foundation
 */

namespace FireHub\Foundation\Temporal\DateTime;

use FireHub\Core\Meta\Enum\Date\ {
    Month, WeekDay
};
use FireHub\Core\Meta\Enum\Date\Format\ {
    Day, Month as MonthFormat, Week, Year
};

/**
 * ### Provides date and time value accessors
 *
 * The Accessors trait provides read-only methods for retrieving individual date, time, timezone, and timestamp
 * components from a DateTime value.
 *
 * It centralizes value access operations while keeping the DateTime class focused on its core temporal behavior.
 * @since 1.0.0
 */
trait Accessors {

    /**
     * ### Get the year of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->year();
     *
     * // 2000
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the year as a string.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Year::NUMBER_LONG To get the year as a long integer.
     *
     * @return int<-9999,9999> The year of the date and time value.
     */
    public function year ():int {

        /** @var int<-9999,9999> */
        return (int)$this->format(Year::NUMBER_LONG);

    }

    /**
     * ### Get the short year of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->yearShort();
     *
     * // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the year as a string.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Year::NUMBER_SHORT To get the year as a short integer.
     *
     * @return int<-99,99> The short year of the date and time value.
     */
    public function yearShort ():int {

        /** @var int<-99,99> */
        return (int)$this->format(Year::NUMBER_SHORT);

    }

    /**
     * ### Get the month of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->month();
     *
     * // Month::JANUARY
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the month as a string.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Month::NUMBER To get the month as an integer.
     *
     * @return \FireHub\Core\Meta\Enum\Date\Month The month of the date and time value.
     */
    public function month ():Month {

        return Month::from((int)$this->format(MonthFormat::NUMBER));

    }

    /**
     * ### Get the padded month of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->monthPadded();
     *
     * // '01'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the month as a string.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Month::NUMBER_PADDED To get the month as a padded integer.
     *
     * @return '01'|'02'|'03'|'04'|'05'|'06'|'07'|'08'|'09'|'10'|'11'|'12' The padded month of the date and time value.
     */
    public function monthPadded ():string {

        /** @var '01'|'02'|'03'|'04'|'05'|'06'|'07'|'08'|'09'|'10'|'11'|'12' */
        return $this->format(MonthFormat::NUMBER_PADDED);

    }

    /**
     * ### Get the number of days in the month of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->daysInMonth();
     *
     * // 31
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the month as a string.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Month::DAYS_IN_MONTH To get the number of days in the month.
     *
     * @return int<0,31> The number of days in the month of the date and time value.
     */
    public function daysInMonth ():int {

        /** @var int<0,31> */
        return (int)$this->format(MonthFormat::DAYS_IN_MONTH);

    }

    /**
     * ### Get the ISO week number of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->weekOfYear();
     *
     * // 52
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the ISO week number.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Week::ISO_NUMBER To get the ISO week number.
     *
     * @return int<1,53> The ISO week number of the date and time value.
     */
    public function weekOfYear ():int {

        /** @var int<1,53> */
        return (int)$this->format(Week::ISO_NUMBER);

    }

    /**
     * ### Get the day of the year of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->dayOfYear();
     *
     * // 0
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the day of the year.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Day::DAY_OF_YEAR To get the day of the year.
     *
     * @return int<1, 366> The day of the year of the date and time value.
     */
    public function dayOfYear ():int {

        /** @var int<1, 366> */
        return (int)$this->format(Day::DAY_OF_YEAR) + 1;

    }

    /**
     * ### Get the week day of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->weekDay();
     *
     * // WeekDay::SATURDAY
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the week day.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Day::DAY_OF_WEEK To get the week day.
     *
     * @return \FireHub\Core\Meta\Enum\Date\WeekDay The week day of the date and time value.
     */
    public function weekDay ():WeekDay {

        return WeekDay::from((int)$this->format(Day::DAY_OF_WEEK));

    }

    /**
     * ### Get the day of the month of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->day();
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the day of the month.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Day::NUMBER To get the day of the month.
     *
     * @return int<1,31> The day of the month of the date and time value.
     */
    public function day ():int {

        /** @var int<1,31> */
        return(int)$this->format(Day::NUMBER);

    }

    /**
     * ### Get the day of the month as a padded integer
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->dayPadded();
     *
     * // '01'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the day as a string.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Day::NUMBER_PADDED To get the day as a padded integer.
     *
     * @return '01'|'02'|'03'|'04'|'05'|'06'|'07'|'08'|'09'|'10'|'11'|'12'|'13'|'14'|'15'|'16'|'17'|'18'|'19'|'20'|'21'|'22'|'23'|'24'|'25'|'26'|'27'|'28'|'29'|'30'|'31' The day of the month as a padded integer.
     */
    public function dayPadded ():string {

        /** @var '01'|'02'|'03'|'04'|'05'|'06'|'07'|'08'|'09'|'10'|'11'|'12'|'13'|'14'|'15'|'16'|'17'|'18'|'19'|'20'|'21'|'22'|'23'|'24'|'25'|'26'|'27'|'28'|'29'|'30'|'31' */
        return $this->format(Day::NUMBER_PADDED);

    }

    /**
     * ### Get the day of the month ordinal suffix
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->dayOrdinalSuffix();
     *
     * // 'st'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the day of the month ordinal suffix.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Day::ORDINAL_SUFFIX To get the day of the month ordinal suffix.
     *
     * @return 'st'|'nd'|'rd'|'th' The day of the month ordinal suffix.
     */
    public function dayOrdinalSuffix ():string {

        /** @var 'st'|'nd'|'rd'|'th' */
        return$this->format(Day::ORDINAL_SUFFIX);

    }

}