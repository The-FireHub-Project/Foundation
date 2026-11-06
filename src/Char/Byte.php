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

namespace FireHub\Foundation\Char;

use FireHub\Core\Type\Char;
use FireHub\Core\Type\Char\Ordinal;
use FireHub\Foundation\Char\Exception\InvalidLengthException;
use FireHub\Runtime;

/**
 * ### Provides an immutable byte value object
 *
 * Represents exactly one raw byte without character-encoding semantics.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\Char<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Byte extends Char {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To check the length of the value.
     *
     * @param string $value <p>
     * The raw byte value.
     * </p>
     *
     * @throws \FireHub\Foundation\Char\Exception\InvalidLengthException If the value does not contain exactly one byte.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Inspection::length($this->value) === 1,
            fn() => new InvalidLengthException
        );

    }

    /**
     * ### Creates a new Byte instance from ordinal value
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Char\SB::chr() To get the character value.
     *
     * @param \FireHub\Core\Type\Char\Ordinal<int<0,255>> $ordinal <p>
     * The ordinal value.
     * </p>
     *
     * @throws \FireHub\Foundation\Char\Exception\InvalidLengthException If the value does not contain exactly one byte.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return static<non-empty-string> Returns a new instance with the character value.
     */
    public static function fromOrdinal (Ordinal $ordinal):static {

        return new static(Runtime\Char\SB::chr($ordinal->value()));

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Char\SB::ord() To get the ordinal value of the byte.
     *
     * @throws \FireHub\Core\Exception\Runtime\System\Invariant\InvalidOrdinalException If the condition is not met.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     */
    public function ordinal ():Ordinal {

        return new Ordinal(Runtime\Char\SB::ord($this->value));

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return non-empty-string The byte value.
     */
    public function value ():string {

        /** @var non-empty-string */
        return $this->value;

    }

}