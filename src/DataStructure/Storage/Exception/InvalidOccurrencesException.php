<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license Proprietary
 *
 * @php-version >=8.3
 * @package Capability
 */

namespace FireHub\Foundation\DataStructure\Storage\Exception;

use FireHub\Core\Exception\Runtime\InvalidArgumentException;

/**
 * ### Represents an attempt to create a storage with an invalid occurrence
 * @since 1.0.0
 */
final class InvalidOccurrencesException extends InvalidArgumentException {

    protected const string DEFAULT_MESSAGE = 'The occurrences is invalid';

}