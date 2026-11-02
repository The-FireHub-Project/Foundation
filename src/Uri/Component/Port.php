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

namespace FireHub\Foundation\Uri\Component;

use FireHub\Core\Type\ValueObject;
use FireHub\Foundation\Uri\Exception\InvalidPortException;

/**
 * ### Represents a network port
 * @since 1.0.0
 *
 * @template TValue of int
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Port extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     *
     * @param int $port <p>
     * Network port.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private int $port
    ) {

        $this->guard(
            fn() => $port >= 0 && $port <= 65535,
            fn() => new InvalidPortException(
                'Port must be between 0 and 65535.'
            )
        );

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @return int<0, 65535> Network port.
     */
    public function value ():int {

        /** @var int<0, 65535> */
        return $this->port;

    }

}