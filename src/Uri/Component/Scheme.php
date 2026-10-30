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
use FireHub\Foundation\Uri\Exception\InvalidSchemeException;
use FireHub\Runtime;

/**
 * ### Represents a URI scheme
 *
 * Preserves the original scheme representation.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Scheme extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the scheme.
     *
     * @param TValue $scheme <p>
     * URI scheme.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidSchemeException If scheme has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $scheme
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '/\A[A-Za-z][A-Za-z0-9+.-]*\z/',
                    $scheme
                ),
            fn() => new InvalidSchemeException
        );

    }

    /**
     * ### Returns the normalized scheme
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert the scheme to lower case.
     *
     * @return lowercase-string&non-empty-string Normalized scheme.
     */
    public function normalized ():string {

        /** @var lowercase-string&non-empty-string */
        return Runtime\Str\SB\Casing::toLower($this->scheme);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->scheme;

    }

    /**
     * ### Compares schemes case-insensitively
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Component\Scheme::normalized() To get the normalized scheme.
     *
     * @return non-empty-string Normalized scheme.
     */
    protected function comparisonValue ():string {

        return $this->normalized();

    }

}