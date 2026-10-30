<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\Uri\Component;

use FireHub\Core\Type\ValueObject;

/**
 * ### Represents a URI authority
 *
 * Combines user information, host, and port into a single immutable URI authority component.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<string>
 */
final readonly class Authority extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\Uri\Component\Host<string> $host <p>
     * URI host.
     * </p>
     * @param null|\FireHub\Foundation\Uri\Component\Port<int> $port [optional] <p>
     * Network port.
     * </p>
     * @param null|\FireHub\Foundation\Uri\Component\UserInfo<string> $user_info [optional] <p>
     * User information.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Host $host,
        private ?Port $port = null,
        private ?UserInfo $user_info = null
    ) {}

    /**
     * ### Gets the host
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Host<string> The host.
     */
    public function host ():Host {

        return $this->host;

    }

    /**
     * ### Gets the port
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Uri\Component\Port<int> The port.
     */
    public function port ():?Port {

        return $this->port;

    }

    /**
     * ### Gets the user information
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Uri\Component\UserInfo<string> The user information.
     */
    public function userInfo ():?UserInfo {

        return $this->user_info;

    }

    /**
     * ### Checks whether the authority is empty
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Host::isEmpty() To check if the host is empty.
     *
     * @return bool True if the authority is empty, false otherwise.
     */
    public function isEmpty ():bool {

        return $this->host->isEmpty()
            && $this->port === null
            && $this->user_info === null;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\UserInfo::value() To get the user information.
     * @uses \FireHub\Foundation\Uri\Component\Host::value() To get the host.
     * @uses \FireHub\Foundation\Uri\Component\Port::value() To get the port.
     */
    public function value ():string {

        $authority = '';

        if ($this->user_info !== null)
            $authority .= $this->user_info->value().'@';

        $authority .= $this->host->value();

        if ($this->port !== null)
            $authority .= ':'.$this->port->value();

        return $authority;

    }

}
