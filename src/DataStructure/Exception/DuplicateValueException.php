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

use FireHub\Core\Exception\Runtime\InvalidArgumentException;

/**
 * ### Represents an attempt to add a duplicate value to a data structure
 * @since 1.0.0
 */
final class DuplicateValueException extends InvalidArgumentException {

    protected const string DEFAULT_MESSAGE = 'The value is already present in the data structure';

}