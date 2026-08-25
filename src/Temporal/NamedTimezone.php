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

use FireHub\Core\Type\Temporal\Timezone;
use FireHub\Core\Type\Date\Zone;
use FireHub\Core\Type\Geo\Country;
use DateTimeZone, DateTimeImmutable;

/**
 *
 * ### Provides an immutable named timezone value object with a high-level developer API
 *
 * The NamedTimezone class represents a named timezone identified by a predefined timezone identifier within the
 * FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with named timezones while preserving immutable
 * value semantics inherited from the Core Timezone Value Object.
 *
 * The class is responsible for high-level named timezone operations and developer experience, while low-level
 * timezone functionality remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of value-of<\FireHub\Core\Type\Date\Zone>
 *
 * @extends \FireHub\Core\Type\Temporal\Timezone<TValue>
 */
readonly class NamedTimezone extends Timezone {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check if the timezone is valid.
     *
     * @param \FireHub\Core\Type\Date\Zone $zone <p>
     * The timezone to set.
     * </p>
     *
     * @return void
     */
    public function __construct (
        protected Zone $zone
    ) {}

    /**
     * ### Gets the country of the timezone
     *
     * <code>
     * use FireHub\Foundation\Temporal\NamedTimezone;
     * use FireHub\Core\Type\Date\Zone;
     *
     * $timezone = new NamedTimezone(Zone::ARCTIC_LONGYEARBYEN)->country();
     *
     * // Country::SVALBARD_AND_JAN_MAYEN
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Core\Type\Geo\Country The country associated with the timezone, or null if none is
     * available.
     */
    public function country ():?Country {

        $location = new DateTimeZone($this->value())->getLocation();

        return $location === false
            ? null
            : Country::fromAlpha2($location['country_code']);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\NamedTimezone;
     * use FireHub\Core\Type\Date\Zone;
     *
     * $timezone = new NamedTimezone(Zone::ARCTIC_LONGYEARBYEN)->value();
     *
     * // 'Arctic/Longyearbyen'
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        /** @var TValue */
        return $this->zone->value;

    }

    /**
     * ### Gets the timezone offset from UTC
     *
     * Returns the fixed timezone offset from UTC in seconds.
     *
     * <code>
     * use FireHub\Foundation\Temporal\FixedTimezone;
     * use FireHub\Foundation\Temporal\DateTime;
     *
     * $timezone = new NamedTimezone(Zone::ARCTIC_LONGYEARBYEN)->offset(DateTime::from('2000-01-01));
     *
     * // 3600
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $at <p>
     * The date/time to calculate the offset for.
     * </p>
     *
     * @return int Offset from UTC in seconds.
     */
    public function offset (DateTime $at):int {

        return new DateTimeZone($this->value())->getOffset(new DateTimeImmutable($at->value()));
    }

}