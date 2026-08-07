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

namespace FireHub\Foundation\Char;

use FireHub\Foundation\Char;
use FireHub\Core\Type\Str\Encoding;
use FireHub\Foundation\Char\Exception\InvalidCharacterException;

/**
 * ### Provides an immutable ASCII character value object
 *
 * The AsciiChar class represents a single ASCII character value within the FireHub Foundation layer.
 *
 * It provides an immutable, type-safe representation of ASCII characters while preserving the character value semantics
 * defined by the Core character type.
 *
 * The class is responsible for enforcing ASCII character constraints and providing the high-level developer API,
 * while low-level character processing remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Foundation\Char<TValue>
 */
readonly class Ascii extends Char {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Foundation\Char::codepoint() To get the codepoint of the character.
     *
     * @param TValue $value <p>
     * The character value.
     * </p>
     * @param \FireHub\Core\Type\Str\Encoding $encoding [optional] <p>
     * The encoding of the character.
     * </p>
     *
     * @throws \FireHub\Foundation\Char\Exception\InvalidCharacterException If the character is not a valid ASCII
     * character.
     * @throws \FireHub\Foundation\Char\Exception\InvalidLengthException If the string length is not 1.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (string $value, Encoding $encoding = self::DEFAULT_ENCODING) {

        parent::__construct($value, $encoding);

        $this->guard(
            fn() => $this->codepoint() <= 0x7F,
            fn() => new InvalidCharacterException(
                'Character must be a valid ASCII character.'
            )
        );

    }

}