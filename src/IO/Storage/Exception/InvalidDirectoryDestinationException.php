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

namespace FireHub\Foundation\IO\Storage\Exception;

/**
 * ### Invalid storage directory destination exception
 *
 * Indicates that a directory destination violates storage hierarchy constraints during a directory transfer operation.
 * @since 1.0.0
 */
final class InvalidDirectoryDestinationException extends StorageException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'Invalid storage directory destination.';

}