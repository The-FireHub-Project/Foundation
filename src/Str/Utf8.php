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

namespace FireHub\Foundation\Str;

use FireHub\Foundation\Str;
use FireHub\Core\Type\Str\Encoding;
use FireHub\Foundation\Str\Exception\InvalidEncodingException;
use FireHub\Runtime;

/**
 * ### UTF-8 string value object
 *
 * Represents an immutable string containing valid UTF-8 encoded text.
 *
 * The encoding is fixed to UTF-8 and cannot be changed.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Foundation\Str<TValue>
 */
readonly class Utf8 extends Str {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Foundation\Str\Ascii::pattern() To check valid UTF-8 pattern.
     *
     * @throws \FireHub\Foundation\Str\Exception\InvalidEncodingException If string is not valid UTF-8 or encoding is
     * not UTF-8.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function __construct (string $value, Encoding $encoding = Encoding::UTF_8) {

        parent::__construct($value, Encoding::UTF_8);

        $this->guard(
            fn() => $encoding === Encoding::UTF_8,
            fn() => new InvalidEncodingException('Strings must use UTF-8 encoding.')
        );

        $this->guard(
            fn() => Runtime\Str\MB\Inspection::checkEncoding($value, Encoding::UTF_8) === true,
            fn() => new InvalidEncodingException('String must be valid UTF-8.')
        );

    }

}