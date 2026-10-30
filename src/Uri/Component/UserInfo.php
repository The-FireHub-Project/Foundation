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
use FireHub\Foundation\Uri\Exception\InvalidUserInfoException;
use FireHub\Runtime;

/**
 * ### Represents URI user information
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class UserInfo extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the userinfo.
     *
     * @param TValue $userinfo <p>
     * Raw URI user information.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUserInfoException If userinfo has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $userinfo
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '/\A(?:[A-Za-z0-9._~!$&\'()*+,;=:-]|%[A-Fa-f0-9]{2})*\z/',
                    $userinfo
                ),
            fn() => new InvalidUserInfoException
        );

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->userinfo;

    }

}