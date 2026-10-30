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
use FireHub\Foundation\Uri\Exception\InvalidHostException;
use FireHub\Runtime;

/**
 * ### Represents a URI host
 *
 * Supports registered names, IPv4 addresses, and bracketed IPv6 literals.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Host extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Foundation\Uri\Component\Host::isValid() To validate the host.
     *
     * @param TValue $host <p>
     * URI host.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidHostException If host has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $host
    ) {

        $this->guard(
            fn() => self::isValid($host),
            fn() => new InvalidHostException
        );

    }

    /**
     * ### Checks whether the host is empty
     * @since 1.0.0
     *
     * @return bool True if the host is empty, false otherwise.
     */
    public function isEmpty ():bool {

        return $this->host === '';

    }

    /**
     * ### Returns the normalized host
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the host to lowercase.
     * @uses \FireHub\Runtime\Str\SB\Casing::toUpper() To convert the host to uppercase.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the host.
     * @uses \FireHub\Runtime\Str\SB\Regex::replaceUsing() To replace the host with the normalized version.
     * @uses \FireHub\Runtime\Char\SB::chr() To get the character at the specified position.
     * @uses \FireHub\Runtime\Number::baseConverter() To convert the hexadecimal value to decimal.
     *
     * @return string Normalized host.
     */
    public function normalized ():string {

        if ($this->host === '')
            return '';

        if ($this->host[0] === '[') {

            if ($this->host[1] === 'v' || $this->host[1] === 'V')
                return $this->host;

            return Runtime\Str\SB\Casing::toLower($this->host);

        }

        $normalized = Runtime\Str\SB\Casing::toLower($this->host);

        return Runtime\Str\SB\Regex::replaceUsing(
            '/%([a-f0-9]{2})/',
            static function (array $matches):string {

                $character = Runtime\Char\SB::chr(
                    (int) Runtime\Number::baseConverter($matches[1], 16, 10) // @phpstan-ignore-line
                );

                if (Runtime\Str\SB\Regex::match('/\A[A-Za-z0-9._~-]\z/', $character))
                    return Runtime\Str\SB\Casing::toLower($character);

                return '%'.Runtime\Str\SB\Casing::toUpper($matches[1]); // @phpstan-ignore offsetAccess.notFound

            },
            $normalized
        );

    }

    /**
     * ### Returns the original host
     */
    public function value ():string {

        return $this->host;

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Host::normalized() To get the normalized host.
     */
    protected function comparisonValue ():string {

        return $this->normalized();

    }

    /**
     * ### Validates a URI host
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::endsWith() To check if the host ends with a closing bracket.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get the address part of the host.
     * @uses \FireHub\Runtime\Filter::value() To validate the address part of the host.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the host.
     *
     * @param string $host <p>
     * URI host.
     * </p>
     *
     * @return bool True if the host is valid, false otherwise.
     */
    private static function isValid (string $host):bool {

        if ($host === '') return true;

        if ($host[0] === '[') {

            if (!Runtime\Str\SB\Search::endsWith(']', $host)) return false;

            $address = Runtime\Str\SB\Access::part($host, 1, -1);

            if (Runtime\Filter::value(
                $address,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_IPV6
                ) !== false) return true;

            return Runtime\Str\SB\Regex::match(
                '/\Av[0-9A-Fa-f]+\.[A-Za-z0-9._~!$&\'()*+,;=:-]+\z/i',
                $address
            );

        }

        return Runtime\Str\SB\Regex::match(
            '/\A(?:[A-Za-z0-9._~!$&\'()*+,;=-]|%[A-Fa-f0-9]{2})*\z/',
            $host
        );

    }

}