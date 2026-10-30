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
use FireHub\Foundation\Uri\Exception\InvalidQueryException;
use FireHub\Runtime;

/**
 * ### Represents a URI query component
 *
 * Preserves the raw query string, including repeated parameters and percent-encoded characters.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Query extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the query.
     *
     * @param TValue $query <p>
     * Raw URI query.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidQueryException If query has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct(
        private string $query
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '/\A(?:[A-Za-z0-9._~!$&\'()*+,;=:@\/?-]|%[A-Fa-f0-9]{2})*\z/',
                    $query
                ),
            fn() => new InvalidQueryException
        );

    }

    /**
     * ### Checks whether the query is empty
     * @since 1.0.0
     *
     * @return bool True if the query is empty, false otherwise.
     */
    public function isEmpty(): bool {

        return $this->query === '';

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value(): string {

        return $this->query;

    }

}