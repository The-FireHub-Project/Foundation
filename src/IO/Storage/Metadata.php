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

namespace FireHub\Foundation\IO\Storage;

use FireHub\Core\Boundary\IO\Storage\ {
    Metadata as MetadataContract, Path as PathContract
};
use FireHub\Core\Type\Temporal\DateTime;
use FireHub\Foundation\IO\Storage\Exception\InvalidObjectSizeException;

/**
 * ### Storage object metadata
 *
 * Represents a read-only snapshot of basic metadata associated with a stored regular object.
 *
 * The snapshot is not automatically updated when the underlying stored object is modified or deleted.
 *
 * <code>
 * $metadata = new Metadata(
 *     new Path('documents/example.txt'),
 *     1024
 * );
 *
 * echo $metadata->size(); // 1024
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Metadata implements MetadataContract {

    /**
     * ### Stored object size
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $size;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Logical path of the stored object.
     * </p>
     * @param int $size <p>
     * Logical object content length in bytes.
     * </p>
     * @param null|\FireHub\Core\Type\Temporal\DateTime<non-empty-string> $last_modified [optional] <p>
     * Last modification time or null if unavailable.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectSizeException
     * If the stored object size is negative.
     *
     * @return void
     */
    public function __construct (
        private PathContract $path,
        int $size,
        private ?DateTime $last_modified = null
    ) {

        if ($size < 0)
            throw new InvalidObjectSizeException(
                'Size cannot be negative.'
            );

        $this->size = $size;

    }

    /**
     * ### Retrieves the logical storage object path
     *
     * <code>
     * echo $metadata->path()->value();
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Core\Boundary\IO\Storage\Path Logical storage object path.
     */
    public function path ():PathContract {

        return $this->path;

    }

    /**
     * ### Retrieves the stored object size
     *
     * <code>
     * echo $metadata->size().' bytes';
     * </code>
     *
     * @since 1.0.0
     *
     * @return non-negative-int Stored object size in bytes.
     */
    public function size ():int {

        return $this->size;

    }

    /**
     * ### Retrieves the last modification time
     *
     * <code>
     * $modified = $metadata->lastModified();
     *
     * if ($modified !== null) {
     *     // Process the last modification time.
     * }
     * </code>
     *
     * @since 1.0.0
     *
     * @return null|\FireHub\Core\Type\Temporal\DateTime<non-empty-string> Last modification time or null if
     * unavailable.
     */
    public function lastModified ():?DateTime {

        return $this->last_modified;

    }

}