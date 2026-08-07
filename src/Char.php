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

namespace FireHub\Foundation;

use FireHub\Core\Type\Char as BaseChar;
use FireHub\Core\Type\Str\Encoding;
use FireHub\Foundation\Str\Boundary\ {
    Caseable, Patternable
};
use FireHub\Foundation\Str\Case\ {
    Casing, Converter
};
use FireHub\Foundation\Str\Pattern;
use FireHub\Foundation\Char\Exception\InvalidLengthException;
use FireHub\Runtime;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Provides an immutable character value object with a high-level developer API
 *
 * The Char class represents a single character value within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with characters while preserving immutable
 * value semantics inherited from the Core Value Object system.
 *
 * The class implements the Core character type contract and extends the base Value Object abstraction, allowing
 * character values to be used consistently across the FireHub ecosystem.
 *
 * This class is responsible for high-level character operations and developer experience, while low-level character
 * handling and encoding operations remain delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\Char<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Char extends BaseChar implements Caseable, Patternable {

    /**
     * ### Base implementation for string-based Value Objects
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\StringValue<TValue>
     */
    use StringValue;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Foundation\Str\Ascii::pattern() To check valid ASCII pattern.
     * @uses \FireHub\Runtime\Str\MB\Inspection::length() To check the length of the string.
     *
     * @param TValue $value <p>
     * The character value.
     * </p>
     * @param \FireHub\Core\Type\Str\Encoding $encoding [optional] <p>
     * The encoding of the character.
     * </p>
     *
     * @throws \FireHub\Foundation\Char\Exception\InvalidLengthException If the string length is not 1.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        protected string $value,
        protected Encoding $encoding = self::DEFAULT_ENCODING
    ) {

        $this->guard(
            fn() => Runtime\Str\MB\Inspection::length($this->value, $this->encoding) === 1,
            fn() => new InvalidLengthException
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function case ():Casing {

        return new Casing($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function pattern (RegexDelimiter $delimiter = RegexDelimiter::SLASH, RegexFlag ...$flags):Pattern {

        return new Pattern($this, $delimiter, ...$flags);

    }

}