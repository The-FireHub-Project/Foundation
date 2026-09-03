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
 * ### Represents the absence of a value
 *
 * None represents a Maybe instance that does not contain a value.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\Maybe<mixed>
 */
final readonly class None extends Maybe {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isSome ():bool {

        return false;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isNone ():bool {

        return true;

    }

}