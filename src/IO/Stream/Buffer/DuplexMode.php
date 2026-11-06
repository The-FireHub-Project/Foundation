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

namespace FireHub\Foundation\IO\Stream\Buffer;

/**
 * ### Buffered Duplex Mode
 *
 * Defines how a buffered stream coordinates transitions between reading  and writing.
 * @since 1.0.0
 */
enum DuplexMode {

    /**
     * ### Shared Position
     *
     * Reading and writing share the same logical position.
     *
     * Switching from reading to writing may require seeking backward to compensate for unread prefetched bytes.
     * @since 1.0.0
     */
    case SHARED_POSITION;

    /**
     * ### Independent Directions
     *
     * Reading and writing operate independently.
     *
     * Unread prefetched data is preserved when switching to writing.
     *
     * Intended for full-duplex transports such as TCP connections.
     * @since 1.0.0
     */
    case INDEPENDENT_DIRECTIONS;

}