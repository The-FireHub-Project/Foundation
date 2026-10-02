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

use FireHub\Core\Type\Temporal\Timezone;
use FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException;
use FireHub\Runtime;
use DateInvalidTimeZoneException, DateTimeZone;

/**
 * ### Provides an immutable fixed-offset timezone value object with a high-level developer API
 *
 * The FixedTimezone class represents a timezone defined by a constant UTC offset within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with numeric timezone offsets while preserving
 * immutable value semantics inherited from the Core Timezone Value Object.
 *
 * The class is responsible for high-level fixed-offset timezone operations and developer experience, while low-level
 * timezone functionality remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\Temporal\Timezone<TValue>
 */
readonly class FixedTimezone extends Timezone {

    /**
     * ### The native timezone
     * @since 1.0.0
     */
    protected DateTimeZone $native;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check if the timezone is valid.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To check the length of the timezone.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get the timezone part.
     *
     * @param TValue $zone <p>
     * The timezone to set.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimeZoneException If the timezone is invalid.
     *
     * @return void
     */
    public function __construct (string $zone) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match('/^[+-](?:[01]\d|2[0-3]):?[0-5]\d$/', $zone),
            fn() => new InvalidTimeZoneException
        );

        if (Runtime\Str\SB\Inspection::length($zone) === 5) {

            /** @var TValue $zone */
            $zone = Runtime\Str\SB\Access::part($zone, 0, 3) // @phpstan-ignore varTag.nativeType
                .':'
                .Runtime\Str\SB\Access::part($zone, 3);

        }

        try {

            $this->native = new DateTimeZone($zone);

        } catch (DateInvalidTimeZoneException) {

            throw new InvalidTimeZoneException;

        }

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\FixedTimezone;
     *
     * $timezone = new FixedTimezone('+05:30')->value();
     *
     * // +05:30
     *
     * $timezone = new FixedTimezone('-0530')->value();
     *
     * // -05:30
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        /** @var TValue */
        return $this->native->getName();

    }

    /**
     * ### Gets the timezone offset from UTC
     *
     * Returns the fixed timezone offset from UTC in seconds.
     *
     * <code>
     * use FireHub\Foundation\Temporal\FixedTimezone;
     *
     * $timezone = FixedTimezone::from('-0100')->offset();
     *
     * // -3600
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get the timezone part.
     *
     * @return int Offset from UTC in seconds.
     */
    public function offset ():int {

        $hours = (int) Runtime\Str\SB\Access::part($this->native->getName(), 1, 2);
        $minutes = (int) Runtime\Str\SB\Access::part($this->native->getName(), 4, 2);

        $offset = ($hours * 3600) + ($minutes * 60);

        return $this->native->getName()[0] === '+'
            ? $offset
            : -$offset;

    }

    /**
     * ### Gets the native timezone
     * @since 1.0.0
     *
     * @return DateTimeZone The native timezone.
     */
    public function native ():DateTimeZone {

        return $this->native;

    }

}