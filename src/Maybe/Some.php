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

namespace FireHub\Foundation\Maybe;

use FireHub\Core\Type\Maybe;

/**
 * ### Represents an existing value
 *
 * Some represent a Maybe instance containing a value.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @extends \FireHub\Core\Type\Maybe<TValue>
 */
final readonly class Some extends Maybe {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TValue $value <p>
     * The value that is present.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private mixed $value
    ) {}

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isSome ():bool {

        return true;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isNone ():bool {

        return false;

    }

    /**
     * ### Returns the contained value
     *
     * @return TValue The contained value.
     */
    public function value ():mixed {

        return $this->value;

    }

}