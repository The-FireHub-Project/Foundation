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

namespace FireHub\Foundation\Temporal\Timespan;

use FireHub\Core\Type\Temporal\Timespan\Components as BaseTimespanComponents;
use FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents;
use FireHub\Runtime;

/**
 * ### Provides immutable temporal Timespan components with a high-level developer API
 *
 * The TimespanComponents class represents the decomposed components of an elapsed timespan within the FireHub
 * Foundation layer.
 *
 * It provides an expressive and immutable interface for inspecting the individual components of a timespan while
 * preserving value semantics inherited from the Core Value Object system.
 *
 * The class is responsible for high-level timespan component representation, while low-level numeric operations
 * remain delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\Temporal\Timespan\Components<array{
 *     days: numeric-string,
 *     hours: int<0,23>,
 *     minutes: int<0,59>,
 *     seconds: int<0,59>,
 *     microseconds: int<0,999999>
 * }>
 *
 * @phpstan-consistent-constructor
 */
readonly class Components extends BaseTimespanComponents {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check if the components are numeric.
     *
     * @param numeric-string $days <p>
     * The number of days.
     * </p>
     * @param int<0,23> $hours <p>
     * The number of hours.
     * </p>
     * @param int<0,59> $minutes <p>
     * The number of minutes.
     * </p>
     * @param int<0,59> $seconds <p>
     * The number of seconds.
     * </p>
     * @param int<0,999999> $microseconds <p>
     * The number of microseconds.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimespanComponents If the components are invalid.
     *
     * @return void
     */
    public function __construct (
        protected string $days,
        protected int $hours,
        protected int $minutes,
        protected int $seconds,
        protected int $microseconds
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match('/^(?:0|[1-9]\d*)$/', $this->days),
            fn() => new InvalidTimespanComponents(
                'The number of days must be a non-negative integer.'
            )
        );

        $this->guard(
            fn() => $this->hours >= 0 && $this->hours <= 23,
            fn() => new InvalidTimespanComponents(
                'The number of hours must be between 0 and 23.'
            )
        );

        $this->guard(
            fn() => $this->minutes >= 0 && $this->minutes <= 59,
            fn() => new InvalidTimespanComponents(
                'The number of minutes must be between 0 and 59.'
            )
        );

        $this->guard(
            fn() => $this->seconds >= 0 && $this->seconds <= 59,
            fn() => new InvalidTimespanComponents(
                'The number of seconds must be between 0 and 59.'
            )
        );

        $this->guard(
            fn() => $this->microseconds >= 0 && $this->microseconds <= 999999,
            fn() => new InvalidTimespanComponents(
                'The number of microseconds must be between 0 and 999999.'
            )
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan\Components;
     *
     * $timespan = new Components('1', 1, 1, 1, 1, 1)->days();
     *
     * // '1'
     * </code>
     *
     * @since 1.0.0
     */
    public function days ():string {

        return $this->days;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan\Components;
     *
     * $timespan = new Components('1', 1, 1, 1, 1, 1)->hours();
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     */
    public function hours ():int {

        return $this->hours;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan\Components;
     *
     * $timespan = new Components('1', 1, 1, 1, 1, 1)->minutes();
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     */
    public function minutes ():int {

        return $this->minutes;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan\Components;
     *
     * $timespan = new Components('1', 1, 1, 1, 1, 1)->seconds();
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     */
    public function seconds ():int {

        return $this->seconds;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan\Components;
     *
     * $timespan = new Components('1', 1, 1, 1, 1, 1)->microseconds();
     *
     * // 1
     * </code>
     *
     * @since 1.0.0
     */
    public function microseconds ():int {

        return $this->microseconds;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Timespan\Components;
     *
     * $timespan = new Components('1', 1, 1, 1, 1)->value();
     *
     * // ['days' => '1', 'hours' => 1, 'minutes' => 1, 'seconds' => 1, 'microseconds' => 1]
     * </code>
     *
     * @since 1.0.0
     *
     * @return array{
     *     days: numeric-string,
     *     hours: int<0,23>,
     *     minutes: int<0,59>,
     *     seconds: int<0,59>,
     *     microseconds: int<0,999999>
     * }
     */
    public function value ():array {

        return [
            'days' => $this->days,
            'hours' => $this->hours,
            'minutes' => $this->minutes,
            'seconds' => $this->seconds,
            'microseconds' => $this->microseconds
        ];

    }

}