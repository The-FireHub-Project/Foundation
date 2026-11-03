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

namespace FireHub\Foundation\Uri;

use FireHub\Core\Type\ValueObject;
use FireHub\Foundation\Uri;
use FireHub\Foundation\Uri\Component\ {
    Host, Port, Scheme
};
use FireHub\Foundation\Uri\Exception\InvalidOriginException;
use FireHub\Runtime;

/**
 * ### URI Origin
 *
 * Represents an immutable tuple-based origin.
 *
 * An origin is identified by its scheme, host, and effective port.
 *
 * Supports HTTP, HTTPS, WS, WSS, and FTP origins.
 *
 * Default ports are normalized during serialization and comparison.
 *
 * The origin does not include URI user information, path, query, or fragment.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\ValueObject<non-empty-string>
 */
final readonly class Origin extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Foundation\Uri\Component\Host::value() To get the host value.
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To normalize the scheme.
     *
     * @param \FireHub\Foundation\Uri\Component\Scheme<non-empty-string> $scheme <p>
     * Origin scheme.
     * </p>
     * @param \FireHub\Foundation\Uri\Component\Host<string> $host <p>
     * Origin host.
     * </p>
     * @param null|\FireHub\Foundation\Uri\Component\Port<int> $port [optional] <p>
     * Explicit origin port or null to use the scheme's default port.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidOriginException If the origin components are invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private Scheme $scheme,
        private Host $host,
        private ?Port $port = null
    ) {

        $scheme_value = Runtime\Str\SB\Casing::toLower($scheme->value());

        $this->guard(
            fn() => match ($scheme_value) {
                KnownScheme::HTTP->value, KnownScheme::HTTPS->value, KnownScheme::WS->value, KnownScheme::WSS->value,
                KnownScheme::FTP->value
                    => true,
                default => false
            },
            fn() => new InvalidOriginException(
                'The URI scheme does not support a tuple-based origin.'
            )
        );

        $this->guard(
            fn() => $host->value() !== '',
            fn() => new InvalidOriginException(
                'An origin must contain a host.'
            )
        );

    }

    /**
     * ### Creates an origin from a URI
     *
     * Extracts the scheme, host, and port from an existing URI.
     *
     * The URI path, query, fragment, and user information do not participate in origin identity.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri::scheme() To get the URI scheme.
     * @uses \FireHub\Foundation\Uri::authority() To get the URI authority.
     * @uses \FireHub\Foundation\Uri\Component\Host::value() To get the host value.
     * @uses \FireHub\Foundation\Uri\Component\Authority::value() To get the authority value.
     * @uses \FireHub\Foundation\Uri\Component\Authority::port() To get the authority port.
     *
     * @param \FireHub\Foundation\Uri $uri <p>
     * URI from which to derive the origin.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidOriginException If the origin components are invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Derived origin.
     */
    public static function fromUri (Uri $uri):self {

        $scheme = $uri->scheme();
        $authority = $uri->authority();

        if ($scheme === null || $authority === null)
            throw new InvalidOriginException(
                'The URI must contain a scheme and authority.'
            );

        $host = $authority->host();

        if ($host->value() === '')
            throw new InvalidOriginException(
                'The URI must contain a host.'
            );

        return new self(
            $scheme,
            $host,
            $authority->port()
        );

    }

    /**
     * ### Gets the origin scheme
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Scheme<non-empty-string> Origin scheme.
     */
    public function scheme ():Scheme {

        return $this->scheme;

    }

    /**
     * ### Gets the origin host
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Host<string> Origin host.
     */
    public function host ():Host {

        return $this->host;

    }

    /**
     * ### Gets the origin port
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Uri\Component\Port<int> Explicit origin port.
     */
    public function port ():?Port {

        return $this->port;

    }

    /**
     * ### Gets the effective origin port
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Port::value() To get the port value.
     * @uses \FireHub\Foundation\Uri\Component\Scheme::known() To get the scheme known information.
     * @uses \FireHub\Foundation\Uri\KnownScheme::defaultPort() To get the default port.
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidOriginException If the origin scheme does not define a
     * default port.
     *
     * @return int Effective origin port.
     */
    public function effectivePort ():int {

        return $this->port?->value()
            ?? $this->scheme->known()?->defaultPort()
            ?? throw new InvalidOriginException(
                'The origin scheme does not define a default port.'
            );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Scheme::normalized() To get the normalized scheme.
     * @uses \FireHub\Foundation\Uri\Component\Host::normalized() To get the normalized host.
     * @uses \FireHub\Foundation\Uri\Component\Port::value() To get the port value.
     * @uses \FireHub\Foundation\Uri\Component\Scheme::known() To get the scheme known information.
     * @uses \FireHub\Foundation\Uri\KnownScheme::defaultPort() To get the default port.
     *
     * @return non-empty-string Serialized origin.
     */
    public function value ():string {

        $scheme = $this->scheme->normalized();
        $host = $this->host->normalized();

        $default_port = $this->scheme->known()?->defaultPort();
        $port = $this->port?->value();

        return $scheme.'://'.$host
            .(
            $port !== null && $port !== $default_port
                ? ':'.$port
                : ''
            );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Scheme::normalized() To normalize the scheme.
     * @uses \FireHub\Foundation\Uri\Component\Host::normalized() To normalize the host.
     * @uses \FireHub\Foundation\Uri\Origin::effectivePort() To get the effective port.
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidOriginException If the origin scheme does not define a
     * default port.
     *
     * @return non-empty-string Normalized origin identity.
     */
    protected function comparisonValue ():string {

        return $this->scheme->normalized()
            .'://'
            .$this->host->normalized()
            .':'
            .$this->effectivePort();

    }

}