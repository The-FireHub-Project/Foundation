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

use FireHub\Core\Meta\Enum\Date\Format\Year;

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

}