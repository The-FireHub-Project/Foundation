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

namespace FireHub\Foundation;

use FireHub\Core\Type\Uri as BaseUri;
use FireHub\Foundation\Uri\Component\ {
    Authority, Fragment, Host, Path, Query, Scheme, UserInfo
};
use FireHub\Foundation\Uri\Exception\InvalidUriException;
use FireHub\Runtime;

/**
 * ### Represents a URI reference
 *
 * Combines URI components into an immutable URI reference according to RFC 3986.
 *
 * Supports both absolute URIs and relative references.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\Uri<string>
 */
final readonly class Uri extends BaseUri {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Foundation\Uri::isValidCombination() To validate the URI component combination.
     *
     * @param null|\FireHub\Foundation\Uri\Component\Scheme<non-empty-string> $scheme [optional] <p>
     * URI scheme.
     * </p>
     * @param null|\FireHub\Foundation\Uri\Component\Authority $authority [optional] <p>
     * URI authority.
     * </p>
     * @param \FireHub\Foundation\Uri\Component\Path<string> $path [optional] <p>
     * URI path.
     * </p>
     * @param null|\FireHub\Foundation\Uri\Component\Query<string> $query [optional] <p>
     * URI query.
     * </p>
     * @param null|\FireHub\Foundation\Uri\Component\Fragment<string> $fragment [optional] <p>
     * URI fragment.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI component combination is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private ?Scheme $scheme = null,
        private ?Authority $authority = null,
        private Path $path = new Path(''), // @phpstan-ignore parameter.defaultValue
        private ?Query $query = null,
        private ?Fragment $fragment = null
    ) {

        $this->guard(
            fn() => $this->isValidCombination(),
            fn() => new InvalidUriException
        );

    }

    /**
     * ### Parses a URI reference
     *
     * Parses an absolute URI or relative reference according to RFC 3986.
     *
     * Preserves the original component representations, including percent-encoded sequences and empty query or
     * fragment components.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Authority::parse() To parse the authority.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To match the URI pattern.
     *
     * @param string $uri <p>
     * URI reference to parse.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUriException If the URI is invalid.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidSchemeException If scheme has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPortException If port is not between 0 and 65535.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidQueryException If query has invalid pattern.
     * @throws \FireHub\Foundation\Uri\Exception\InvalidFragmentException If fragment has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Parsed URI reference.
     */
    public static function parse (string $uri):self {

        if (!Runtime\Str\SB\Regex::match(
            '/\A(?:([A-Za-z][A-Za-z0-9+.-]*):)?(?:\/\/([^\/?#]*))?([^?#]*)(?:\?([^#]*))?(?:#(.*))?\z/s',
            $uri,
            result: $matches,
            flags: PREG_UNMATCHED_AS_NULL
        )) throw new InvalidUriException;

        return new self(
            $matches[1] !== null // @phpstan-ignore offsetAccess.notFound
                ? new Scheme($matches[1]) // @phpstan-ignore argument.type
                : null,
            $matches[2] !== null // @phpstan-ignore offsetAccess.notFound
                ? Authority::parse($matches[2]) // @phpstan-ignore argument.type
                : null,
            new Path($matches[3]), // @phpstan-ignore offsetAccess.notFound, argument.type
            $matches[4] !== null // @phpstan-ignore offsetAccess.notFound
                ? new Query($matches[4]) // @phpstan-ignore argument.type
                : null,
            $matches[5] !== null // @phpstan-ignore offsetAccess.notFound
                ? new Fragment($matches[5]) // @phpstan-ignore argument.type
                : null
        );

    }

    /**
     * ### Gets the scheme
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Uri\Component\Scheme<non-empty-string> URI scheme.
     */
    public function scheme ():?Scheme {

        return $this->scheme;

    }

    /**
     * ### Gets the authority
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Uri\Component\Authority URI authority.
     */
    public function authority ():?Authority {

        return $this->authority;

    }

    /**
     * ### Gets the path
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Uri\Component\Path<string> URI path.
     */
    public function path ():Path {

        return $this->path;

    }

    /**
     * ### Gets the query
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Uri\Component\Query<string> URI query.
     */
    public function query ():?Query {

        return $this->query;

    }

    /**
     * ### Gets the fragment
     * @since 1.0.0
     *
     * @return null|\FireHub\Foundation\Uri\Component\Fragment<string> URI fragment.
     */
    public function fragment ():?Fragment {

        return $this->fragment;

    }

    /**
     * ### Checks whether the URI is absolute
     *
     * An absolute URI contains a scheme and does not contain a fragment.
     * @since 1.0.0
     *
     * @return bool True if the URI is absolute, false otherwise.
     */
    public function isAbsolute ():bool {

        return $this->scheme !== null && $this->fragment === null;

    }

    /**
     * ### Checks whether the URI reference is relative
     * @since 1.0.0
     *
     * @return bool True if the URI reference has no scheme, false otherwise.
     */
    public function isRelative ():bool {

        return $this->scheme === null;

    }

    /**
     * ### Normalizes the URI
     *
     * Applies syntax-based normalization according to RFC 3986 and removes default ports for supported schemes.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Scheme::normalized() To normalize the scheme.
     * @uses \FireHub\Foundation\Uri\Component\Authority::normalized() To normalize the authority.
     * @uses \FireHub\Foundation\Uri\Component\Path::normalized() To normalize the path.
     * @uses \FireHub\Foundation\Uri\Component\Query::normalized() To normalize the query.
     * @uses \FireHub\Foundation\Uri\Component\Fragment::normalized() To normalize the fragment.
     * @uses \FireHub\Foundation\Uri\Component\Path::removeDotSegments() To remove dot segments.
     *
     * @return self Normalized URI.
     */
    public function normalize ():self {

        $scheme = $this->scheme !== null
            ? new Scheme($this->scheme->normalized())
            : null;

        $authority = null;

        if ($this->authority !== null) {

            $host = new Host(
                $this->authority->host()->normalized()
            );

            $port = $this->authority->port();

            $user_info = $this->authority->userInfo();

            $authority = new Authority(
                $host,
                $port,
                $user_info !== null
                    ? new UserInfo($user_info->normalized())
                    : null
            );

        }

        $path = new Path($this->path->normalized());

        if ($scheme !== null || $authority !== null)
            $path = $path->removeDotSegments();

        return new self(
            $scheme,
            $authority,
            $path,
            $this->query !== null
                ? new Query($this->query->normalized())
                : null,
            $this->fragment !== null
                ? new Fragment($this->fragment->normalized())
                : null
        );

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Scheme::value() To get the scheme value.
     * @uses \FireHub\Foundation\Uri\Component\Authority::value() To get the authority value.
     * @uses \FireHub\Foundation\Uri\Component\Path::value() To get the path value.
     * @uses \FireHub\Foundation\Uri\Component\Query::value() To get the query value.
     * @uses \FireHub\Foundation\Uri\Component\Fragment::value() To get the fragment value.
     */
    public function value ():string {

        $uri = '';

        if ($this->scheme !== null)
            $uri .= $this->scheme->value().':';

        if ($this->authority !== null)
            $uri .= '//'.$this->authority->value();

        $uri .= $this->path->value();

        if ($this->query !== null)
            $uri .= '?'.$this->query->value();

        if ($this->fragment !== null)
            $uri .= '#'.$this->fragment->value();

        return $uri;

    }

    /**
     * ### Validates the URI component combination
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Path::value() To get the path value.
     * @uses \FireHub\Foundation\Uri\Component\Path::isAbsolute() To check if the path is absolute.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the path starts with "//".
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check if the path contains a colon.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the path into segments.
     *
     * @return bool True if the combination is valid, false otherwise.
     */
    private function isValidCombination ():bool {

        $path = $this->path->value();

        if ($this->authority !== null)
            return $path === '' || $this->path->isAbsolute();

        if (Runtime\Str\SB\Search::startsWith('//', $path))
            return false;

        if ($this->scheme === null && $path !== '' && $path[0] !== '/') {

            $first_segment = Runtime\Str\SB\Delimiter::explode($path, '/', 2)[0]; // @phpstan-ignore offsetAccess.notFound

            if (Runtime\Str\SB\Search::contains(':', $first_segment))
                return false;

        }

        return true;

    }

}