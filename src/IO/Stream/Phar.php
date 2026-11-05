<?php declare(strict_types=1);

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

use FireHub\Core\Boundary\IO\Stream\ {
    Readable, Writable
};
use FireHub\Core\Meta\Enum\Side;
use FireHub\Foundation\IO\Stream\Exception\StreamException;
use FireHub\Runtime\Stream\Enum\OpenMode;
use FireHub\Runtime;

/**
 * ### PHAR Archive Entry Stream
 *
 * Provides a readable and writable stream backed by PHP's phar:// stream wrapper.
 *
 * Allows reading and writing individual files stored inside PHAR-compatible archives without extracting the entire
 * archive.
 *
 * Reading and writing capabilities depend on the selected opening mode, archive format, and PHP configuration.
 *
 * Writing to executable PHAR archives may be restricted by the phar.readonly configuration directive.
 *
 * This abstraction does not expose archive creation, deletion, signing, or metadata management operations.
 *
 * Requires the PHP Phar extension.
 * @since 1.0.0
 */
final class Phar extends ResourceStream implements Readable, Writable {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Stream\Handle::open() To open the PHAR archive entry.
     * @uses \FireHub\Runtime\System\Extension::hasExtension() To validate the extension availability.
     *
     * @param non-empty-string $archive <p>
     * Path to the PHAR-compatible archive.
     * </p>
     * @param non-empty-string $entry <p>
     * Path to the file inside the archive.
     * </p>
     * @param \FireHub\Runtime\Stream\Enum\OpenMode $mode [optional] <p>
     * Stream opening mode.
     *
     * Defaults to READ_BINARY.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the Phar extension is unavailable,
     * the archive or entry path is invalid, or the stream cannot be opened.
     *
     * @return void
     *
     */
    public function __construct(string $archive, string $entry, OpenMode $mode = OpenMode::READ_BINARY) {

        if (!Runtime\System\Extension::hasExtension('phar'))
            throw new StreamException(
                'The Phar extension is required to open PHAR archive streams.'
            );

        if ($archive === '')
            throw new StreamException(
                'The PHAR archive path cannot be empty.'
            );

        if ($entry === '')
            throw new StreamException(
                'The PHAR archive entry path cannot be empty.'
            );

        $resource = Runtime\Stream\Handle::open(
            'phar://' . $archive . '/' . Runtime\Str\SB\Transform::trim($entry, Side::LEFT, '/'),
            $mode
        );

        if ($resource === false)
            throw new StreamException(
                'Unable to open PHAR archive entry stream.'
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceIsWritable() To check if the resource is writable.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function isWritable ():bool {

        return $this->resourceIsWritable();

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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::readResourceContents() To read the remaining contents of the resource.
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
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::writeResource() To write to the resource.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If writing fails.
     */
    public function write (string $data):int {

        return $this->writeResource($data);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Stream\ResourceStream::resourceEof() To check if the resource has reached EOF.
     *
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the stream is closed.
     */
    public function eof ():bool {

        return $this->resourceEof();

    }

}