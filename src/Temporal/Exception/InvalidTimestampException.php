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

namespace FireHub\Foundation\Temporal\Exception;

use FireHub\Core\Exception\DomainException;

/**
 * ### Represents an invalid timestamp provided to an operation that requires a valid timestamp
 * @since 1.0.0
 */
final class InvalidTimestampException extends DomainException {

    protected const string DEFAULT_MESSAGE = 'The timestamp is not valid';

}