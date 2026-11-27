<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\IO\Storage\Internal;

use FireHub\Core\Meta\Enum\FileSystem\Permission;
use FireHub\Core\Type\FileSystem\PermissionMode;
use FireHub\Core\Foundation\Constant\Environment\Php\Paths;
use FireHub\Foundation\IO\Storage\Permissions;
use FireHub\Foundation\IO\Stream\File;
use FireHub\Foundation\IO\Storage\Exception\WriteException;
use FireHub\Runtime;
use Closure, Throwable;

/**
 * ### Atomic filesystem writer
 *
 * Writes contents to a temporary file before replacing the destination file.
 *
 * The temporary file is created within the destination directory to avoid cross-filesystem replacement operations.
 *
 * Existing destination contents remain unchanged if writing fails before the replacement operation.
 *
 * Atomic replacement depends on the underlying filesystem and operating system.
 * @since 1.0.0
 *
 * @internal
 */
final readonly class AtomicWriter {

    /**
     * ### Temporary filename prefix
     * @since 1.0.0
     *
     * @var non-empty-string
     */
    private const string PREFIX = '.firehub-';

    /**
     * ### Writes contents through a temporary file
     *
     * Creates a temporary file, invokes the supplied writer, closes the temporary stream, and replaces the
     * destination file.
     *
     * The writer must complete synchronously and must not close or retain the supplied stream.
     *
     * <code>
     * AtomicWriter::write(
     *     __DIR__.'/storage/example.txt',
     *     static function (Writable $stream):void {
     *
     *         $stream->write('Contents');
     *
     *     }
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::temporaryPath() To generate a temporary filesystem
     * path.
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::cleanup() To remove the temporary file after a
     * failed operation.
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::permissionMode() To determine the destination file
     * permission mode.
     * @uses \FireHub\Foundation\IO\Stream\File::close() To close the temporary file stream.
     * @uses \FireHub\Runtime\FileSystem\File::rename() To replace the destination file.
     * @uses \FireHub\Runtime\FileSystem\Metadata::setPermissions() To set the temporary file permission mode.
     *
     * @param non-empty-string $destination <p>
     * Resolved physical destination path.
     * </p>
     * @param \Closure(\FireHub\Core\Boundary\IO\Stream\Writable):void $writer <p>
     * Callback responsible for writing the complete contents.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\DeleteFileException If we couldn't delete the file.
     * @throws \FireHub\Foundation\IO\Stream\Exception\StreamException If the file cannot be opened.
     * @throws \FireHub\Foundation\IO\Storage\Exception\WriteException If the writer fails.
     *
     * @return void
     */
    public static function write (string $destination, Closure $writer, Permissions $permissions = new Permissions):void {

        $temporary = null; $stream = null; $created = false;
        try {

            $mode = self::permissionMode(
                $destination,
                $permissions
            );

            $temporary = self::temporaryPath($destination);

            $stream = new File(
                $temporary,
                Runtime\Stream\Enum\OpenMode::CREATE_BINARY
            );

            $created = true;

            Runtime\FileSystem\Metadata::setPermissions(
                $temporary,
                new PermissionMode(
                    Permission::READ_WRITE,
                    Permission::NONE,
                    Permission::NONE
                )
            );

            $writer($stream);

            $stream->close();

            $stream = null;

            Runtime\FileSystem\Metadata::setPermissions(
                $temporary,
                $mode
            );

            Runtime\FileSystem\File::rename(
                $temporary,
                $destination
            );

            $created = false;

        } catch (Throwable $exception) {

            throw new WriteException(
                previous: $exception
            );

        } finally {

            try {

                $stream?->close();

            } finally {

                if ($created && $temporary !== null)
                    self::cleanup($temporary);

            }

        }

    }

    /**
     * ### Generates a temporary filesystem path
     *
     * Generates a randomized temporary filename within the destination directory.
     *
     * The generated path is not reserved until the temporary file is opened using an exclusive creation mode.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::PREFIX To prefix the temporary filename.
     * @uses \FireHub\Core\Foundation\Constant\Environment\Php\Paths::DIRECTORY_SEPARATOR To separate the path segments.
     * @uses \FireHub\Runtime\FileSystem\Path::parent() To retrieve the destination directory.
     * @uses \FireHub\Runtime\Encoding::hexEncode() To generate a randomized hexadecimal string.
     * @uses \FireHub\Runtime\Random::bytes() To generate a random byte sequence.
     *
     * @param non-empty-string $destination <p>
     * Physical destination path.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\InvalidPathParentLevelException If $levels is less than 1.
     * @throws \FireHub\Runtime\Exception\RandomByteLengthTooSmallException If the length is less than 1.
     * @throws \FireHub\Runtime\Exception\SecureRandomBytesException If a cryptographically secure source of randomness
     * is unavailable.
     *
     * @return non-empty-string Temporary filesystem path.
     */
    private static function temporaryPath (string $destination):string {

        return Runtime\FileSystem\Path::parent($destination)
            .Paths::DIRECTORY_SEPARATOR
            .self::PREFIX
            .Runtime\Encoding::hexEncode(Runtime\Random::bytes(16))
            .'.tmp';

    }

    /**
     * ### Determines the destination permission mode
     *
     * Preserves the standard permission bits of an existing destination file when required by the storage permission
     * policy.
     *
     * Otherwise, returns the configured default file permission mode.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Permissions::preserveExisting() To determine whether existing permissions
     * should be preserved.
     * @uses \FireHub\Foundation\IO\Storage\Permissions::file() To retrieve the default file permission mode.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To invalidate cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\Metadata::getPermissions() To retrieve existing file permissions.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To determine whether the destination file exists.
     *
     * @param non-empty-string $destination <p>
     * Resolved physical destination path.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Permissions $permissions <p>
     * Filesystem permission policy.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\PathPermissionsException If failed to permissions for a path.
     * @throws \FireHub\Runtime\Exception\InvalidNumberBaseException If failed to convert a number to a base.
     *
     * @return \FireHub\Core\Type\FileSystem\PermissionMode Destination file permission mode.
     */
    private static function permissionMode (string $destination, Permissions $permissions):PermissionMode {

        Runtime\FileSystem\Metadata::clearCache(
            true,
            $destination
        );

        return $permissions->preserveExisting() && Runtime\FileSystem\File::isFile($destination)
            ? Runtime\FileSystem\Metadata::getPermissions($destination)
            : $permissions->file();

    }

    /**
     * ### Removes a temporary file
     *
     * Attempts to remove a temporary file without replacing the original writing exception if cleanup fails.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\FileSystem\File::delete() To remove the temporary file.
     *
     * @param non-empty-string $temporary <p>
     * Physical temporary file path.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\DeleteFileException If we couldn't delete the file.
     *
     * @return void
     */
    private static function cleanup (string $temporary):void {

        Runtime\FileSystem\File::delete($temporary);

    }

}