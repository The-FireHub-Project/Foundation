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
 * ### Combination size mismatch exception
 *
 * Thrown when two data sources being combined contain a different number of elements.
 * @since 1.0.0
 */
final class CombinationSizeMismatch extends InvalidArgumentException {

    protected const string DEFAULT_MESSAGE = 'The combination size mismatch';

}