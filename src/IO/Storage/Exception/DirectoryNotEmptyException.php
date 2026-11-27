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
 * ### Storage directory is not empty exception
 *
 * Indicates that a storage directory contains filesystem objects that prevent an operation requiring an empty
 * directory.
 * @since 1.0.0
 */
final class DirectoryNotEmptyException extends StorageException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'Storage directory is not empty.';

}