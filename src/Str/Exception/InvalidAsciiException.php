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

namespace FireHub\Foundation\Str\Exception;

use FireHub\Core\Exception\DomainException;

/**
 * ### Represents an invalid character provided to an operation that requires ASCII string
 * @since 1.0.0
 */
final class InvalidAsciiException extends DomainException {

    protected const string DEFAULT_MESSAGE = 'Invalid characters in ASCII string';

}