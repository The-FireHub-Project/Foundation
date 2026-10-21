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

namespace FireHub\Foundation\Algorithm\Sorting\Exception;

use FireHub\Core\Exception\RuntimeException;

/**
 * ### Counting sort range exception
 * @since 1.0.0
 */
final class CountingSortRangeException extends RuntimeException {

    protected const string DEFAULT_MESSAGE = 'The range of the counting sort algorithm is invalid.';

}