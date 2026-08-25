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

}