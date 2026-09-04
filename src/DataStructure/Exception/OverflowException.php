<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.3
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Exception;

use FireHub\Core\Exception\RuntimeException;

/**
 * ### Represents an attempt to add an element to a full data structure
 * @since 1.0.0
 */
final class OverflowException extends RuntimeException {

    protected const string DEFAULT_MESSAGE = 'The data structure is full';

}