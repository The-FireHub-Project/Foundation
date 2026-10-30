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
use FireHub\Foundation\Uri\Encoding\Percent;
use FireHub\Foundation\Uri\Exception\InvalidUserInfoException;
use FireHub\Runtime;

/**
 * ### Represents URI user information
 * @since 1.0.0
 *
 * @template TValue of string
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
     * @param TValue $user_info <p>
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
        private string $user_info
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '/\A(?:[A-Za-z0-9._~!$&\'()*+,;=:-]|%[A-Fa-f0-9]{2})*\z/',
                    $user_info
                ),
            fn() => new InvalidUserInfoException
        );

    }

    /**
     * ### Creates a URI user info from a decoded string
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\Percent::encodeQuery() To encode the user info.
     *
     * @param string $user_info <p>
     * Decoded URI user info.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidUserInfoException If userinfo has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<string> Encoded URI user info.
     */
    public static function fromDecoded (string $user_info):self {

        return new self(Percent::encodeQuery($user_info));

    }

    /**
     * ### Gets the decoded URI user info
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\PercentEncoder::decode() To decode the user info.
     *
     * @return string Decoded URI user info.
     */
    public function decoded ():string {

        return Percent::decode($this->user_info);

    }

    /**
     * ### Checks whether the user information is empty
     * @since 1.0.0
     *
     * @return bool True if the user information is empty, false otherwise.
     */
    public function isEmpty(): bool {

        return $this->user_info === '';

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->user_info;

    }

}