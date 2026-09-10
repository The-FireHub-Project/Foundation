<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.0
 * @package Core
 */

namespace FireHub\Foundation\Conversion\Exception;

use FireHub\Core\Exception\RuntimeException;

/**
 * ### Represents a failure caused by a value could not be converted
 * @since 1.0.0
 */
final class ConversionException extends RuntimeException {

    protected const string DEFAULT_MESSAGE = 'The provided value could not be converted.';

}