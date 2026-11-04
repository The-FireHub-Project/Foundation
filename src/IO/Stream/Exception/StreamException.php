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

namespace FireHub\Foundation\IO\Stream\Exception;

use FireHub\Core\Exception\RuntimeException;

/**
 * ### Stream Exception
 *
 * Represents an error occurring during an input or output stream operation.
 * @since 1.0.0
 */
final class StreamException extends RuntimeException {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    protected const string DEFAULT_MESSAGE = 'An error occurred while processing the stream.';

}