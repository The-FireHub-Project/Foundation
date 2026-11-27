<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Storage\Internal\Memory;

use FireHub\Foundation\Temporal\DateTime;
use FireHub\Runtime;

/**
 * ### In-memory stored object
 *
 * Represents an immutable snapshot of the contents and modification time of an object stored within an in-memory
 * storage driver.
 *
 * The storage driver manages the logical storage path independently.
 *
 * <code>
 * $object = new StoredObject(
 *     'Contents',
 *     DateTime::now()
 * );
 * </code>
 *
 * @since 1.0.0
 *
 * @internal
 */
final readonly class StoredObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param string $contents <p>
     * Binary-safe stored object contents.
     * </p>
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $last_modified <p>
     * Last modification time of the stored object.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private string $contents,
        private DateTime $last_modified
    ) {}

    /**
     * ### Retrieves the stored object contents
     *
     * <code>
     * echo $object->contents();
     *
     * // Contents
     * </code>
     *
     * @since 1.0.0
     *
     * @return string Complete binary-safe stored object contents.
     */
    public function contents ():string {

        return $this->contents;

    }

    /**
     * ### Retrieves the stored object size
     *
     * The size represents the logical content length in bytes, rather than the memory allocation size.
     *
     * <code>
     * echo $object->size();
     *
     * // 8
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To determine the stored object content length in bytes.
     *
     * @return non-negative-int Stored object size in bytes.
     */
    public function size ():int {

        return Runtime\Str\SB\Inspection::length($this->contents);

    }

    /**
     * ### Retrieves the stored object last modification time
     *
     * <code>
     * echo $object->lastModified()->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Temporal\DateTime<non-empty-string> Last modification time of the stored object.
     */
    public function lastModified ():DateTime {

        return $this->last_modified;

    }

    /**
     * ### Returns a new stored object with the specified contents
     *
     * The original stored object remains unchanged.
     *
     * <code>
     * $updated = $object->withContents(
     *     'Updated contents',
     *     DateTime::now()
     * );
     *
     * echo $updated->contents();
     *
     * // Updated contents
     * </code>
     *
     * @since 1.0.0
     *
     * @param string $contents <p>
     * New binary-safe stored object contents.
     * </p>
     * @param \FireHub\Foundation\Temporal\DateTime<non-empty-string> $last_modified <p>
     * Last modification time of the new stored object.
     * </p>
     *
     * @return self New stored object instance.
     */
    public function withContents (string $contents, DateTime $last_modified):self {

        return new self($contents, $last_modified);

    }

}