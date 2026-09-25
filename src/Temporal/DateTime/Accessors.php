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
    ExtendedUnit, Month, Unit, WeekDay
};
use FireHub\Core\Meta\Enum\Date\Format\ {
    Day, Month as MonthFormat, Time, Week, Year
};
use FireHub\Runtime;

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
     * ### Get the zero-based millennium index of the date and time value
     *
     * Returns the zero-based millennium index containing the year of the date and time value.
     *
     * The index is calculated using astronomical year numbering, where year 0 belongs to index 0, positive years
     * advance through later indexes, and negative years occupy preceding indexes.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * $datetime->millenniumIndex();
     *
     * // 2
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::MILLENNIUM To define the millennium unit.
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::factor() To get the number of years in a millennium.
     * @uses \FireHub\Runtime\Math::floor() To calculate the floor of a number.
     *
     * @return int The zero-based millennium index.
     */
    public function millenniumIndex ():int {

        return Runtime\Math::floor(($this->year()) / ExtendedUnit::MILLENNIUM->factor());

    }

    /**
     * ### Get the millennium of the date and time value
     *
     * Returns the ordinal number of the millennium containing the year of the date and time value.
     *
     * The millennium is calculated using astronomical year numbering, where year 0 represents 1 BCE, year -1
     * represents 2 BCE, and year 1 represents 1 CE.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * $datetime->millennium();
     *
     * // 3
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime\Accessors::millenniumIndex() To get the zero-based millennium index.
     *
     * @return int The ordinal number of the millennium.
     */
    public function millennium ():int {

        return $this->millenniumIndex() + 1;

    }

    /**
     * ### Get the zero-based century index of the date and time value
     *
     * Returns the zero-based century index containing the year of the date and time value.
     *
     * The index is calculated using astronomical year numbering, where year 0 belongs to index 0, positive years
     * advance through later indexes, and negative years occupy preceding indexes.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * $datetime->centuryIndex();
     *
     * // 20
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::CENTURY To define the century unit.
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::factor() To get the number of years in a century.
     * @uses \FireHub\Runtime\Math::floor() To calculate the floor of a number.
     *
     * @return int The zero-based century index.
     */
    public function centuryIndex ():int {

        return Runtime\Math::floor(
            $this->year() / ExtendedUnit::CENTURY->factor()
        );

    }

    /**
     * ### Get the century of the date and time value
     *
     * Returns the ordinal number of the century containing the year of the date and time value.
     *
     * The century is calculated using astronomical year numbering, where year 0 represents 1 BCE, year -1
     * represents 2 BCE, and year 1 represents 1 CE.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * $datetime->century();
     *
     * // 21
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime\Accessors::centuryIndex() To get the zero-based century index.
     *
     * @return int The ordinal number of the century.
     */
    public function century ():int {

        return $this->centuryIndex() + 1;

    }

    /**
     * ### Get the zero-based decade index of the date and time value
     *
     * Returns the zero-based decade index containing the year of the date and time value.
     *
     * The index is calculated using astronomical year numbering, where year 0 belongs to index 0, positive years
     * advance through later indexes, and negative years occupy preceding indexes.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * $datetime->decadeIndex();
     *
     * // 200
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::DECADE To define the decade unit.
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::factor() To get the number of years in a decade.
     * @uses \FireHub\Runtime\Math::floor() To calculate the floor of a number.
     *
     * @return int The zero-based decade index.
     */
    public function decadeIndex ():int {

        return Runtime\Math::floor(
            $this->year() / ExtendedUnit::DECADE->factor()
        );

    }

    /**
     * ### Get the decade of the date and time value
     *
     * Returns the ordinal number of the decade containing the year of the date and time value.
     *
     * The decade is calculated using astronomical year numbering, where year 0 represents 1 BCE, year -1
     * represents 2 BCE, and year 1 represents 1 CE.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * $datetime->decade();
     *
     * // 201
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime\Accessors::decadeIndex() To get the zero-based decade index.
     *
     * @return int The ordinal number of the decade.
     */
    public function decade ():int {

        return $this->decadeIndex() + 1;

    }

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
     * ### Get the quarter of the date and time value
     *
     * Returns the ordinal number of the calendar quarter containing the date and time value.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * $datetime->quarter();
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::month() To get the calendar month.
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::QUARTER To define the quarter unit.
     * @uses \FireHub\Core\Meta\Enum\Date\ExtendedUnit::factor() To get the number of months in a quarter.
     *
     * @return int<1,4> The ordinal number of the calendar quarter.
     */
    public function quarter ():int {

        /** @var int<1,4> */
        return Runtime\Math::floor(($this->month()->value - 1) / ExtendedUnit::QUARTER->factor()) + 1;

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
     * @return int<28,31> The number of days in the month of the date and time value.
     */
    public function daysInMonth ():int {

        /** @var int<28,31> */
        return (int)$this->format(MonthFormat::DAYS_IN_MONTH);

    }

    /**
     * ### Get the fortnight of the date and time value
     *
     * Returns the ordinal number of the fortnight containing the date and time value.
     *
     * The fortnight is calculated from the beginning of the calendar year, with each fortnight representing a period of
     * 14 consecutive calendar days.
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00');
     *
     * $datetime->fortnight();
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::dayOfYear() To get the zero-based day of the year.
     * @uses \FireHub\Runtime\Math::floor() To calculate the floor of a number.
     *
     * @return int<1,27> The ordinal number of the fortnight.
     */
    public function fortnight ():int {

        /** @var int<1,27> */
        return Runtime\Math::floor(($this->dayOfYear() - 1) / 14) + 1;

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
     * // 1
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
     * ### Get the day of the month of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:00:00')->dayOfMonth();
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
    public function dayOfMonth ():int {

        /** @var int<1,31> */
        return(int)$this->format(Day::NUMBER);

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

    /**
     * ### Get the hour of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 18:00:00')->hour();
     *
     * // 18
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the hour of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::HOUR_24 To get the hour of the date and time value.
     *
     * @return int<0,23> The hour of the date and time value.
     */
    public function hour ():int {

        /** @var int<0,23> */
        return (int)$this->format(Time::HOUR_24);

    }

    /**
     * ### Get the padded hour of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 18:00:00')->hourPadded();
     *
     * // '18'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the padded hour of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::HOUR_24_PADDED To get the padded hour of the date and time value.
     *
     * @return non-empty-string The padded hour of the date and time value.
     */
    public function hourPadded ():string {

        return $this->format(Time::HOUR_24_PADDED);

    }

    /**
     * ### Get the hour of the date and time value in 12-hour format
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 18:00:00')->hour12();
     *
     * // 6
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the hour of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::HOUR_12 To get the hour of the date and time value.
     *
     * @return int<1,12> The hour of the date and time value in 12-hour format.
     */
    public function hour12 ():int {

        /** @var int<1,12> */
        return (int)$this->format(Time::HOUR_12);

    }

    /**
     * ### Get the padded hour of the date and time value in 12-hour format
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 18:00:00')->hour12Padded();
     *
     * // '06'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the padded hour of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::HOUR_12 To get the padded hour of the date and time value.
     *
     * @return non-empty-string The padded hour of the date and time value in 12-hour format.
     */
    public function hour12Padded ():string {

        return $this->format(Time::HOUR_12_PADDED);

    }

    /**
     * ### Get the meridiem of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 18:00:00')->meridiem();
     *
     * // 'PM'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the meridiem of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::MERIDIEM_UPPER To get the meridiem of the date and time value.
     *
     * @return 'AM'|'PM' The meridiem of the date and time value.
     */
    public function meridiem ():string {

        /** @var 'AM'|'PM' */
        return $this->format(Time::MERIDIEM_UPPER);

    }

    /**
     * ### Get the lowercased meridiem of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 18:00:00')->meridiemLower();
     *
     * // 'pm'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the lowercased meridiem of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::MERIDIEM_LOWER To get the lowercased meridiem of the date and time value.
     *
     * @return 'am'|'pm' The lowercased meridiem of the date and time value.
     */
    public function meridiemLower ():string {

        /** @var 'am'|'pm' */
        return $this->format(Time::MERIDIEM_LOWER);

    }

    /**
     * ### Get the minute of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:13:14.123888')->minute();
     *
     * // 13
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the minute of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::MINUTE To get the minute of the date and time value.
     *
     * @return int<0,59> The minute of the date and time value.
     */
    public function minute ():int {

        /** @var int<0,59> */
        return (int)$this->format(Time::MINUTE);

    }

    /**
     * ### Get the second of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:13:14.123888')->second();
     *
     * // 14
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the second of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::SECOND To get the second of the date and time value.
     *
     * @return int<0,59> The second of the date and time value.
     */
    public function second ():int {

        /** @var int<0,59> */
        return (int)$this->format(Time::SECOND);

    }

    /**
     * ### Get the millisecond of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:13:14.123888')->millisecond();
     *
     * // 123
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the millisecond of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::MILLISECOND To get the millisecond of the date and time value.
     *
     * @return int<0,999> The millisecond of the date and time value.
     */
    public function millisecond ():int {

        /** @var int<0,999> */
        return (int)$this->format(Time::MILLISECOND);

    }

    /**
     * ### Get the microsecond of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:13:14.123888')->microsecond();
     *
     * // 123888
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the microsecond of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::MICROSECOND To get the microsecond of the date and time value.
     *
     * @return int<0,999999> The microsecond of the date and time value.
     */
    public function microsecond ():int {

        /** @var int<0,999999> */
        return (int)$this->format(Time::MICROSECOND);

    }

    /**
     * ### Get the Swatch Internet Time of the date and time value
     *
     * <code>
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $datetime = DateTime::from('2000-01-01 12:13:14.123888')->swatchBeat();
     *
     * // 550
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::format() To get the Swatch Internet Time of the date and time value.
     * @uses \FireHub\Core\Meta\Enum\Date\Format\Time::SWATCH_BEAT To get the Swatch Internet Time of the date and time value.
     *
     * @return int<0,999> The Swatch Internet Time of the date and time value.
     */
    public function swatchBeat ():int {

        /** @var int<0,999> */
        return (int)$this->format(Time::SWATCH_BEAT);

    }

}