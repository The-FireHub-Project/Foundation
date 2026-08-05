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

use FireHub\Foundation\Str\Trait\Instantiable;
use FireHub\Foundation\Str\Boundary\ {
    Caseable, CaseTransformable, Patternable
};
use FireHub\Foundation\Str\Case\ {
    Casing, Converter
};
use FireHub\Core\Type\Str\Encoding;
use FireHub\Foundation\Str\Exception\InvalidAsciiException;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Represents an ASCII encoded string value object
 *
 * Provides an immutable string value object that enforces ASCII character encoding semantics.
 *
 * This class ensures that the represented string value contains only characters supported by the ASCII encoding
 * while providing the common string behavior inherited from the base string value object implementation.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Foundation\Str\Base<TValue>
 */
readonly class Ascii extends Base implements Caseable, CaseTransformable, Patternable {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Foundation\Str\Ascii::pattern() To check valid ASCII pattern.
     *
     * @throws \FireHub\Foundation\Str\Exception\InvalidAsciiException If string is not valid ASCII.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function __construct (string $value, Encoding $encoding = self::DEFAULT_ENCODING) {

        parent::__construct($value, $encoding);

        $this->guard(
            fn() => $this->pattern()->match()->is()->ascii() === true,
            fn() => new InvalidAsciiException('String must be valid ASCII.')
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
     * ### Instantiable
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\Str\Trait\Instantiable<TValue>
     */
    use Instantiable;

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function transform ():Converter {

        return new Converter($this);

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