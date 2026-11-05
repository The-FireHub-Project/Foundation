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

use FireHub\Core\Type\ {
    Char as BaseChar, Str
};

/**
 * ### Provides an immutable binary string value object
 *
 * The Bytes class represents an immutable sequence of bytes within the FireHub Foundation layer.
 *
 * Unlike text-oriented string value objects, this class operates directly on raw bytes without interpreting
 * character encodings.
 *
 * Each byte can contain any value between 0 and 255, including null bytes and values that do not represent valid
 * text characters.
 *
 * All positions and lengths are measured in bytes rather than characters.
 *
 * This class is suitable for binary data, encoded content, network payloads, file contents, and other byte-oriented
 * operations.
 * @since 1.0.0
 */
readonly class Bytes extends Str {

}