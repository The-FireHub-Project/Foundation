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
use FireHub\Core\Meta\Enum\Side;
use FireHub\Foundation\Uri\Exception\InvalidUriException;
use FireHub\Runtime;

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
     * ### Parses a URI authority
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::lastPosition() To find the last occurrence of a specific character.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the first occurrence of a specific character.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get the part of the string.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the string starts with a specific character.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check if the string contains a specific character.
     * @uses \FireHub\Foundation\Uri\Authority::parsePort() To parse the port.
     *
     * @param string $authority <p>
     * URI authority to parse.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the authority or port is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed URI authority.
     */
    public static function parse (string $authority):self {

        $user_info = null;

        $separator = Runtime\Str\SB\Search::lastPosition('@', $authority);

        if ($separator !== false) {

            $user_info = new UserInfo(
                Runtime\Str\SB\Access::part($authority, 0, $separator)
            );

            $authority = Runtime\Str\SB\Access::part($authority, $separator + 1);

        }

        $host = $authority; $port = null;
        if (Runtime\Str\SB\Search::startsWith('[', $authority)) {

            $closing = Runtime\Str\SB\Search::firstPosition(']', $authority);

            if ($closing === false)
                throw new InvalidUriException;

            $host = Runtime\Str\SB\Access::part($authority, 0, $closing + 1);

            $remaining = Runtime\Str\SB\Access::part($authority, $closing + 1);

            if ($remaining !== '') {

                if (!Runtime\Str\SB\Search::startsWith(':', $remaining))
                    throw new InvalidUriException;

                $port = self::parsePort(Runtime\Str\SB\Access::part($remaining, 1));

            }

        } else if (Runtime\Str\SB\Search::contains(':', $authority)) {

            $separator = Runtime\Str\SB\Search::lastPosition(':', $authority) ?: 0;

            $host = Runtime\Str\SB\Access::part($authority, 0, $separator);

            if (Runtime\Str\SB\Search::contains(':', $host))
                throw new InvalidUriException;

            $port = self::parsePort(
                Runtime\Str\SB\Access::part($authority, $separator + 1)
            );

        }

        return new self(
            new Host($host),
            $port,
            $user_info
        );

    }

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

    /**
     * ### Parses a URI port
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check if the port is valid.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the port.
     *
     * @param string $port <p>
     * Raw URI port.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the port is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|\FireHub\Foundation\Uri\Component\Port<int> Parsed port.
     */
    private static function parsePort (string $port):?Port {

        if ($port === '') return null;

        if (!Runtime\Str\SB\Regex::match('/\A[0-9]+\z/', $port))
            throw new InvalidUriException;

        $normalized = Runtime\Str\SB\Transform::trim($port, Side::LEFT, '0');

        return new Port((int)$normalized);

    }

}