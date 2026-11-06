<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Stream;

/**
 * ### Stream Resource Ownership
 *
 * Defines whether a stream implementation is responsible for closing its underlying resource.
 * @since 1.0.0
 */
enum Ownership {

    /**
     * ### Owned resource
     *
     * The stream is responsible for closing the underlying resource.
     * @since 1.0.0
     */
    case OWNED;

    /**
     * ### Borrowed resource
     *
     * The stream must not close the underlying resource.
     * @since 1.0.0
     */
    case BORROWED;

}