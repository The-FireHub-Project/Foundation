<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Stream\Filter;

use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime;

/**
 * ### Stream Filter Attachment
 *
 * Represents a native PHP stream filter attached to an existing stream.
 *
 * Provides explicit filter removal and tracks whether the attachment remains active.
 *
 * The attachment does not own the underlying stream.
 *
 * Closing the parent stream invalidates its filter attachments.
 * @since 1.0.0
 */
final class Attachment {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param null|resource $resource <p>
     * Native PHP stream filter resource.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private mixed $resource = null
    ) {}

    /**
     * ### Checks whether the filter is active
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\DataIs::resource() To validate the filter resource.
     *
     * @return bool True if the filter is active.
     */
    public function isActive ():bool {

        return Runtime\DataIs::resource($this->resource);

    }

    /**
     * ### Removes the filter from its stream
     *
     * Removes the filter from the native PHP stream filter chain.
     *
     * Calling this method multiple times has no additional effect.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Filter::remove() To remove the native stream filter.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If removing the filter fails.
     *
     * @return void
     */
    public function remove ():void {

        if (!$this->isActive()) return;

        /** @var resource $resource */
        $resource = $this->resource;

        if (!Runtime\Stream\Filter::remove($resource))
            throw new StreamException(
                'Unable to remove stream filter.'
            );

        $this->resource = null;

    }

    /**
     * ### Invalidates the filter attachment
     *
     * Marks the attachment as inactive without attempting to remove the native filter.
     *
     * Intended for use when the parent stream is closed.
     * @since 1.0.0
     *
     * @return void
     */
    public function invalidate ():void {

        $this->resource = null;

    }

}