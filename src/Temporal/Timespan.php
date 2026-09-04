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

namespace FireHub\Foundation\Temporal;

use FireHub\Core\Type\Temporal\ {
    Timespan\Components as BaseComponents, Timespan as BaseTimespan
};
use FireHub\Foundation\Temporal\Timespan\Components;
use FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks;
use FireHub\Runtime;

/**
 * ### Provides an immutable temporal Timespan Value Object with a high-level developer API
 *
 * The Timespan class represents an elapsed amount of time between two temporal points within the FireHub Foundation
 * layer.
 *
 * It provides an expressive and object-oriented interface for creating, inspecting, and manipulating elapsed time
 * while preserving immutable value semantics inherited from the Core Value Object system.
 *
 * The class is responsible for high-level timespan operations and developer experience, while low-level temporal
 * functionality remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of numeric-string
 *
 * @extends \FireHub\Core\Type\Temporal\Timespan<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Timespan extends BaseTimespan {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check if the ticks value is numeric.
     *
     * @param TValue $ticks <p>
     * The number of ticks representing the elapsed time.
     *
     * One tick represents one microsecond.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks If the ticks value is not numeric.
     *
     * @return void
     */
    public function __construct (
        protected string $ticks
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match('/^(?:0|-?[1-9]\d*)$/', $this->ticks),
            fn() => new InvalidTimespanTicks
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('386400000000')->components();
     *
     * // ['days' => '4 'hours' => 11, 'minutes' => 20, 'seconds' => 0, 'microseconds' => 0] // Components oobject
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::divide() To convert ticks to days.
     * @uses \FireHub\Runtime\Math\DecimalEngine::mod() To get the remainder after dividing by days.
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents If the components are invalid.
     */
    public function components ():BaseComponents {

        $ticks = $this->ticks;

        $days = Runtime\Math\DecimalEngine::divide($ticks, '86400000000', 0);
        $ticks = Runtime\Math\DecimalEngine::mod($ticks, '86400000000');

        $hours = Runtime\Math\DecimalEngine::divide($ticks, '3600000000');
        /** @var int<0,23> $hours */
        $hours = (int)$hours;
        $ticks = Runtime\Math\DecimalEngine::mod($ticks, '3600000000');

        $minutes = Runtime\Math\DecimalEngine::divide($ticks, '60000000');
        /** @var int<0,59> $minutes */
        $minutes = (int)$minutes;
        $ticks = Runtime\Math\DecimalEngine::mod($ticks, '60000000');

        $seconds = Runtime\Math\DecimalEngine::divide($ticks, '1000000');
        /** @var int<0,59> $seconds */
        $seconds = (int)$seconds;
        $microseconds = Runtime\Math\DecimalEngine::mod($ticks, '1000000');
        /** @var int<0,999999> $microseconds */
        $microseconds = (int)$microseconds;


        return new Components(
            $days,
            $hours,
            $minutes,
            $seconds,
            $microseconds
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('100')->value();
     *
     * // '100'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Temporal\DateTime::value() To get the timespan value.
     */
    public function value ():string {

        return $this->ticks;

    }

    /**
     * ### Adds a number of microseconds to the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('100')->addMicroseconds('1')->value();
     *
     * // '101'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::add() To add the microseconds to the ticks value.
     *
     * @param numeric-string $value <p>
     * The number of microseconds to add to the timespan.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks If the ticks value is not numeric.
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is not a valid
     * decimal number.
     *
     * @return static<numeric-string> Returns a new Timespan instance with the updated ticks value.
     */
    public function addMicroseconds (string $value):static {

        return new static(Runtime\Math\DecimalEngine::add($this->ticks, $value));

    }

    /**
     * ### Adds a number of milliseconds to the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('100')->addMilliseconds('1')->value();
     *
     * // '1100'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::multiply() To convert milliseconds to microseconds.
     * @uses \FireHub\Runtime\Math\DecimalEngine::add() To add the microseconds to the ticks value.
     *
     * @param numeric-string $value <p>
     * The number of milliseconds to add to the timespan.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks If the ticks value is not numeric.
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is not a valid
     * decimal number.
     *
     * @return static<numeric-string> Returns a new Timespan instance with the updated ticks value.
     */
    public function addMilliseconds (string $value):static {

        return new static(
            Runtime\Math\DecimalEngine::add(
                $this->ticks,
                Runtime\Math\DecimalEngine::multiply($value, '1000')
            )
        );

    }

    /**
     * ### Adds a number of seconds to the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('100')->addSeconds('1')->value();
     *
     * // '1000100'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::multiply() To convert seconds to microseconds.
     * @uses \FireHub\Runtime\Math\DecimalEngine::add() To add the microseconds to the ticks value.
     *
     * @param numeric-string $value <p>
     * The number of seconds to add to the timespan.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks If the ticks value is not numeric.
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is not a valid
     * decimal number.
     *
     * @return static<numeric-string> Returns a new Timespan instance with the updated ticks value.
     */
    public function addSeconds (string $value):static {

        return new static(
            Runtime\Math\DecimalEngine::add(
                $this->ticks,
                Runtime\Math\DecimalEngine::multiply($value, '1000000')
            )
        );

    }

    /**
     * ### Adds a number of minutes to the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('100')->addMinutes('1')->value();
     *
     * // '60000100'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::multiply() To convert minutes to microseconds.
     * @uses \FireHub\Runtime\Math\DecimalEngine::add() To add the microseconds to the ticks value.
     *
     * @param numeric-string $value <p>
     * The number of minutes to add to the timespan.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks If the ticks value is not numeric.
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is not a valid
     * decimal number.
     *
     * @return static<numeric-string> Returns a new Timespan instance with the updated ticks value.
     */
    public function addMinutes (string $value):static {

        return new static(
            Runtime\Math\DecimalEngine::add(
                $this->ticks,
                Runtime\Math\DecimalEngine::multiply($value, '60000000')
            )
        );

    }

    /**
     * ### Adds a number of hours to the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('100')->addHours('1')->value();
     *
     * // '3600000100'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::multiply() To convert hours to microseconds.
     * @uses \FireHub\Runtime\Math\DecimalEngine::add() To add the microseconds to the ticks value.
     *
     * @param numeric-string $value <p>
     * The number of hours to add to the timespan.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks If the ticks value is not numeric.
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is not a valid
     * decimal number.
     *
     * @return static<numeric-string> Returns a new Timespan instance with the updated ticks value.
     */
    public function addHours (string $value):static {

        return new static(
            Runtime\Math\DecimalEngine::add(
                $this->ticks,
                Runtime\Math\DecimalEngine::multiply($value, '3600000000')
            )
        );

    }

    /**
     * ### Adds a number of days to the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('100')->addDays('1')->value();
     *
     * // '86400000100'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::multiply() To convert days to microseconds.
     * @uses \FireHub\Runtime\Math\DecimalEngine::add() To add the microseconds to the ticks value.
     *
     * @param numeric-string $value <p>
     * The number of days to add to the timespan.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanTicks If the ticks value is not numeric.
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is not a valid
     * decimal number.
     *
     * @return static<numeric-string> Returns a new Timespan instance with the updated ticks value.
     */
    public function addDays (string $value):static {

        return new static(
            Runtime\Math\DecimalEngine::add(
                $this->ticks,
                Runtime\Math\DecimalEngine::multiply($value, '86400000000')
            )
        );

    }

    /**
     * ### Returns the number of microseconds in the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $datetime = new Timespan('86400000000')->microseconds();
     *
     * // '86400000000'
     * </code>
     *
     * @since 1.0.0
     *
     * @return numeric-string Returns the number of microseconds in the timespan.
     */
    public function microseconds ():string {

        return $this->ticks;

    }

    /**
     * ### Returns the number of milliseconds in the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $timespan = new Timespan('86400000000')->milliseconds();
     *
     * // '86400000'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::divide() To convert ticks to milliseconds.
     *
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is invalid.
     * @throws \FireHub\Runtime\Exception\InvalidScaleNumberException If the scale is less than zero.
     * @throws \DivisionByZeroError If the divisor is zero.
     *
     * @return numeric-string Returns the number of milliseconds in the timespan.
     */
    public function milliseconds ():string {

        return Runtime\Math\DecimalEngine::divide($this->ticks, '1000');

    }

    /**
     * ### Returns the number of seconds in the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $timespan = new Timespan('86400000000')->seconds();
     *
     * // '86400'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::divide() To convert ticks to seconds.
     *
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is invalid.
     * @throws \FireHub\Runtime\Exception\InvalidScaleNumberException If the scale is less than zero.
     * @throws \DivisionByZeroError If the divisor is zero.
     *
     * @return numeric-string Returns the number of seconds in the timespan.
     */
    public function seconds ():string {

        return Runtime\Math\DecimalEngine::divide($this->ticks, '1000000');

    }

    /**
     * ### Returns the number of minutes in the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $timespan = new Timespan('86400000000')->minutes();
     *
     * // '1440'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::divide() To convert ticks to minutes.
     *
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is invalid.
     * @throws \FireHub\Runtime\Exception\InvalidScaleNumberException If the scale is less than zero.
     * @throws \DivisionByZeroError If the divisor is zero.
     *
     * @return numeric-string Returns the number of minutes in the timespan.
     */
    public function minutes ():string {

        return Runtime\Math\DecimalEngine::divide($this->ticks, '60000000');

    }

    /**
     * ### Returns the number of hours in the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $timespan = new Timespan('86400000000')->hours();
     *
     * // '24'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::divide() To convert ticks to hours.
     *
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is invalid.
     * @throws \FireHub\Runtime\Exception\InvalidScaleNumberException If the scale is less than zero.
     * @throws \DivisionByZeroError If the divisor is zero.
     *
     * @return numeric-string Returns the number of hours in the timespan.
     */
    public function hours ():string {

        return Runtime\Math\DecimalEngine::divide($this->ticks, '3600000000');

    }

    /**
     * ### Returns the number of days in the timespan
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan;
     *
     * $timespan = new Timespan('86400000000')->days();
     *
     * // '1'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Math\DecimalEngine::divide() To convert ticks to days.
     *
     * @throws \FireHub\Runtime\Exception\InvalidDecimalNumberException If either decimal value is invalid.
     * @throws \FireHub\Runtime\Exception\InvalidScaleNumberException If the scale is less than zero.
     * @throws \DivisionByZeroError If the divisor is zero.
     *
     * @return numeric-string Returns the number of days in the timespan.
     */
    public function days ():string {

        return Runtime\Math\DecimalEngine::divide($this->ticks, '86400000000');

    }

}