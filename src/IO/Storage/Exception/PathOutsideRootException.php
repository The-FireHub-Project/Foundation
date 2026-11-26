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
 * ### Path outside storage root exception
 *
 * Thrown when a resolved filesystem path points outside the configured storage root directory.
 * @since 1.0.0
 */
final class PathOutsideRootException extends StorageException {

    /**
     * @since 1.0.0
     *
     * @var string Default exception message.
     */
    protected const string DEFAULT_MESSAGE = 'Path points outside the storage root directory';

}