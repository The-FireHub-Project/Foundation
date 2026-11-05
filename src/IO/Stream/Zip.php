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

namespace FireHub\Foundation\IO\Stream;

use FireHub\Core\Boundary\IO\Stream\Readable;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime\Stream\Enum\OpenMode;
use FireHub\Runtime;

/**
 * ### ZIP Archive Entry Stream
 *
 * Provides a readable stream backed by PHP's zip:// stream wrapper.
 *
 * Allows reading individual files stored inside ZIP archives without extracting the entire archive.
 *
 * The stream is read-only and does not guarantee seeking or positioning capabilities.
 *
 * Creating, modifying, and deleting ZIP archive entries are not supported by this abstraction.
 *
 * Requires the PHP zip extension.
 * @since 1.0.0
 */
final class Zip extends ResourceStream implements Readable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the ZIP archive entry.
     * @uses \FireHub\Runtime\System\Extension::hasExtension() To validate the extension availability.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To validate the entry path.
     *
     * @param non-empty-string $archive <p>
     * Path to the ZIP archive.
     * </p>
     * @param non-empty-string $entry <p>
     * Path to the file inside the ZIP archive.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the extension is unavailable, the archive
     * or entry path is invalid, or the entry cannot be opened.
     *
     * @return void
     */
    public function __construct (string $archive, string $entry) {

        if (!Runtime\System\Extension::hasExtension('zip'))
            throw new StreamException(
                'The zip extension is required to open ZIP archive streams.'
            );

        if ($archive === '')
            throw new StreamException(
                'The ZIP archive path cannot be empty.'
            );

        if ($entry === '')
            throw new StreamException(
                'The ZIP archive entry path cannot be empty.'
            );

        if (Runtime\Str\SB\Search::contains('#', $archive))
            throw new StreamException(
                'The ZIP archive path cannot contain the entry separator (#).'
            );

        $resource = Runtime\Stream\Handle::open(
            "zip://$archive#$entry",
            OpenMode::READ_BINARY
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open ZIP archive entry stream.'
            );

        parent::__construct($resource, Ownership::OWNED);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsReadable() To check if the resource is readable.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function isReadable ():bool {

        return $this->resourceIsReadable();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::readResource() To read from the resource.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     */
    public function read (int $length):string {

        return $this->readResource($length);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::readResourceContents() To read the remaining contents.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If reading fails.
     */
    public function contents ():string {

        return $this->readResourceContents();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceEof() To check whether EOF has been reached.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function eof ():bool {

        return $this->resourceEof();

    }

}