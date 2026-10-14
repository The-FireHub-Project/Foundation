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

namespace FireHub\Foundation\Identifier;

use FireHub\Core\Type\Identifier;
use FireHub\Foundation\Identifier\Exception\InvalidIdentifierValueException;
use FireHub\Runtime;
use Stringable;

/**
 * ### Defines the base named identifier Value Object
 *
 * Represents an immutable, non-empty textual identifier used to identify components and other named elements within
 * the FireHub ecosystem.
 *
 * The class enforces the fundamental invariant that an identifier name cannot be empty or consist entirely of
 * whitespace.
 *
 * Concrete implementations may introduce additional restrictions, such as allowed characters, naming conventions, or
 * maximum length.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\Identifier<TValue>
 */
abstract readonly class Name extends Identifier implements Stringable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\Identifier::guard() To enforce the invariant that an identifier name cannot be empty.
     * @uses \FireHub\Runtime\Str\SB\Transform::trim() To trim the identifier name.
     *
     * @param TValue $value <p>
     * The identifier name.
     * </p>
     *
     * @throws \FireHub\Foundation\Identifier\Exception\InvalidIdentifierValueException If the identifier name is empty.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Transform::trim($value),
            fn() => new InvalidIdentifierValueException(
                'Identifier name cannot be empty.'
            )
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

    /**
     * ### Returns the string representation of the identifier
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\IdentifierName::value() To get the identifier name.
     *
     * @return non-empty-string Identifier name.
     */
    public function __toString ():string {

        return $this->value();

    }

}