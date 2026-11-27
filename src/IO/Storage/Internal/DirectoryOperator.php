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

namespace FireHub\Foundation\IO\Storage\Internal;

use FireHub\Core\Boundary\IO\Storage\Path as PathContract;
use FireHub\Core\Boundary\IO\Stream\Writable;
use FireHub\Core\Foundation\Constant\Environment\Php\ {
    Paths, PlatformInfo
};
use FireHub\Foundation\IO\Storage\ {
    Path, Permissions
};
use FireHub\Foundation\IO\Stream\ {
    Copier, File
};
use FireHub\Core\Exception\FireHubException;
use FireHub\Foundation\IO\Storage\Exception\ {
    DirectoryAlreadyExistsException, DirectoryCopyException, DirectoryCreationException, DirectoryDeletionException,
    DirectoryNotEmptyException, DirectoryNotFoundException, InvalidDirectoryDestinationException,
    InvalidObjectTypeException, ReservedPathException, SymbolicLinkNotAllowedException
};
use FireHub\Runtime;

/**
 * ### Storage directory operator
 *
 * Coordinates physical directory operations within a local storage root directory.
 *
 * Directory operations respect logical path validation, storage root boundaries, and configured directory permission
 * policies.
 * @since 1.0.0
 *
 * @internal
 */
final readonly class DirectoryOperator {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Foundation\IO\Storage\Internal\PathResolver $resolver <p>
     * Storage filesystem path resolver.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Permissions $permissions <p>
     * Storage filesystem permission policy.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private PathResolver $resolver,
        private Permissions $permissions
    ) {}

    /**
     * ### Determines whether a storage directory exists
     *
     * Determines whether the specified logical path identifies an existing physical directory.
     *
     * If no directory is specified, the storage root is inspected. A path identifying a regular file produces false.
     *
     * <code>
     * $operator->exists(new Path('documents'));
     *
     * $operator->exists();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::root() To retrieve the physical storage root
     * directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the physical directory path.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To determine whether the physical directory exists.
     *
     * @param null|\FireHub\Core\Boundary\IO\Storage\Path $directory [optional] <p>
     * Logical directory path or null to inspect the storage root.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return bool True if the specified storage directory exists.
     */
    public function exists (?PathContract $directory = null):bool {

        $physical = $directory === null
            ? $this->resolver->root()
            : $this->resolver->resolve($directory);

        Runtime\FileSystem\Metadata::clearCache(true, $physical);

        return Runtime\FileSystem\Folder::exists($physical);

    }

    /**
     * ### Creates a storage directory
     *
     * Creates a physical directory at the specified logical storage path.
     *
     * Creating an existing directory has no effect. Recursive creation includes all missing parent directories.
     *
     * Newly created directories use the configured storage directory permission policy.
     *
     * Recursive directory creation is not guaranteed to be atomic.
     *
     * <code>
     * $operator->create(new Path('documents'));
     *
     * $operator->create(
     *     new Path('documents/archive/2026'),
     *     recursive: true
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To validate and resolve the requested
     * directory path.
     * @uses \FireHub\Foundation\IO\Storage\Path::parent() To retrieve the logical parent directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::exists() To determine whether the parent
     * directory exists.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::create() To recursively create missing parent
     * directories.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::createDirectory() To create the physical
     * directory.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To determine whether the requested directory already exists.
     * @uses \FireHub\Runtime\FileSystem\File::exist() To detect conflicting filesystem objects.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical path of the directory to create.
     * </p>
     * @param bool $recursive [optional] <p>
     * Whether missing parent directories should also be created.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\DirectoryNotFoundException If the parent directory does not
     * exist and recursive creation is not allowed.
     * @throws \FireHub\Runtime\Exception\CreateFolderException If we couldn't create a folder.
     * @throws \FireHub\Runtime\Exception\PathPermissionsException If failed to set permissions for a path.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    public function create (PathContract $directory, bool $recursive = false):void {

        $directory = $directory instanceof Path
            ? $directory
            : new Path($directory->value());

        $physical = $this->resolver->resolve($directory);

        Runtime\FileSystem\Metadata::clearCache(true, $physical);

        if (Runtime\FileSystem\Folder::exists($physical))
            return;

        if (Runtime\FileSystem\File::exist($physical))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $directory->value()
                ]
            );

        $parent = $directory->parent();

        if (!$this->exists($parent)) {

            if (!$recursive || $parent === null)
                throw new DirectoryNotFoundException(
                    context: ['path' => $parent?->value()]
                );

            $this->create($parent, true);

        }

        $this->createDirectory($directory);

    }

    /**
     * ### Deletes a storage directory
     *
     * Deletes the physical directory identified by the specified logical storage path.
     *
     * Deleting a nonexistent directory has no effect. If recursive deletion is disabled, the directory must be empty.
     *
     * Recursive deletion removes all supported descendant filesystem objects and directories.
     *
     * Symbolic links, reserved temporary files, and unsupported filesystem object types prevent recursive deletion.
     *
     * Recursive deletion is not guaranteed to be atomic.
     *
     * <code>
     * $operator->delete(new Path('empty'));
     *
     * $operator->delete(
     *     new Path('documents/archive'),
     *     recursive: true
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Path::__construct()
     * To normalize the logical directory path.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve()
     * To resolve the physical directory path.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache()
     * To clear cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\File::exist()
     * To determine whether the filesystem object exists.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists()
     * To determine whether the filesystem object is a directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::entries()
     * To enumerate physical directory entries.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::validateTree()
     * To validate the directory tree before recursive deletion.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::deleteTree()
     * To recursively delete the directory tree.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::deleteDirectory()
     * To delete an empty physical directory.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $directory <p>
     * Logical path of the directory to delete.
     * </p>
     * @param bool $recursive [optional] <p>
     * Whether descendant objects and directories should also be deleted.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    public function delete (PathContract $directory, bool $recursive = false):void {

        $directory = $directory instanceof Path
            ? $directory
            : new Path($directory->value());

        $physical = $this->resolver->resolve($directory);

        Runtime\FileSystem\Metadata::clearCache(true, $physical);

        if (!Runtime\FileSystem\File::exist($physical))
            return;

        if (!Runtime\FileSystem\Folder::exists($physical))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $directory->value()
                ]
            );

        if ($recursive) {

            $this->validateTree($directory);
            $this->deleteTree($directory);

        } else {

            foreach ($this->entries($physical) as $entry)
                throw new DirectoryNotEmptyException(
                    context: [
                        'path' => $directory->value(),
                        'entry' => $entry
                    ]
                );

            $this->deleteDirectory($directory);

        }

    }

    /**
     * ### Copies a storage directory
     *
     * Recursively copies the specified source directory and its descendant regular files and directories to a new
     * destination.
     *
     * Empty directories are preserved. The destination parent directory must already exist. Copying an existing
     * directory to itself has no effect.
     *
     * Copying a directory into one of its descendants is not permitted. Symbolic links, reserved temporary files,
     * and unsupported filesystem object types are not copied.
     *
     * Newly created filesystem objects use the configured storage permission policy.
     *
     * Directory copying is not guaranteed to be atomic.
     *
     * <code>
     * $operator->copy(
     *     new Path('documents'),
     *     new Path('backup')
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Path::__construct() To normalize logical storage paths.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::validateDestination() To validate the source
     * and destination directory relationship.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::validateTree() To validate the source
     * directory tree before copying.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::copyTree() To recursively copy the directory
     * tree.
     *
     * @param \FireHub\Core\Boundary\IO\Storage\Path $source <p>
     * Logical path of the source directory.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Storage\Path $destination <p>
     * Logical path of the destination directory.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    public function copy (PathContract $source, PathContract $destination):void {

        $source = $source instanceof Path
            ? $source
            : new Path($source->value());

        $destination = $destination instanceof Path
            ? $destination
            : new Path($destination->value());

        if (!$this->validateDestination($source, $destination))
            return;

        $this->validateTree($source);

        $this->copyTree($source, $destination);

    }

    /**
     * ### Creates a physical storage directory
     *
     * Resolves the logical directory path immediately before creation and applies the configured directory
     * permission policy.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To resolve the physical directory path.
     * @uses \FireHub\Foundation\IO\Storage\Permissions::directory() To retrieve the configured directory permission
     * mode.
     * @uses \FireHub\Runtime\FileSystem\Folder::create() To create the physical directory.
     * @uses \FireHub\Runtime\FileSystem\Metadata::setPermissions() To apply the configured directory permission mode.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $directory <p>
     * Logical path of the directory to create.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\CreateFolderException If we couldn't create a folder.
     * @throws \FireHub\Runtime\Exception\PathPermissionsException If failed to set permissions for a path.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    private function createDirectory (Path $directory):void {

        $physical = $this->resolver->resolve($directory);
        $permissions = $this->permissions->directory();

        try {

            Runtime\FileSystem\Folder::create(
                $physical,
                $permissions
            );

            Runtime\FileSystem\Metadata::setPermissions(
                $physical,
                $permissions
            );

        } catch (FireHubException $exception) {

            throw new DirectoryCreationException(
                context: [
                    'path' => $directory->value()
                ],
                previous: $exception
            );

        } finally {

            Runtime\FileSystem\Metadata::clearCache(true, $physical);

        }

    }

    /**
     * ### Enumerates physical directory entries
     *
     * Lists all physical directory entries except the current and parent directory references.
     *
     * Hidden files, symbolic links, reserved names, and unsupported filesystem object types are not filtered.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\FileSystem\Folder::list() To enumerate physical directory entries.
     *
     * @param non-empty-string $physical <p>
     * Physical directory path.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\CannotListFolderException If $folder is empty, or we couldn't list files
     * and directories inside the specified folder.
     *
     * @return iterable<int, non-empty-string> Physical directory entry names.
     */
    private function entries (string $physical):iterable {

        foreach (Runtime\FileSystem\Folder::list($physical) as $name)

            if ($name !== '' && $name !== '.' && $name !== '..')
                yield $name;

    }

    /**
     * ### Resolves a physical directory entry
     *
     * Validates a directory entry and resolves its logical storage path.
     *
     * Symbolic links, reserved names, and unsupported filesystem object types are not permitted.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::isTemporary() To identify reserved temporary
     * filenames.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\Link::symbolicExists() To identify symbolic links.
     * @uses \FireHub\Foundation\IO\Storage\Path::join() To construct the logical descendant path.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To safely resolve the logical descendant
     * path.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To identify physical directories.
     * @uses \FireHub\Runtime\FileSystem\File::isFile() To identify regular files.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $directory <p>
     * Logical parent directory path.
     * </p>
     * @param non-empty-string $physical <p>
     * Physical parent directory path.
     * </p>
     * @param non-empty-string $name <p>
     * Physical directory entry name.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return array{
     *     path: \FireHub\Foundation\IO\Storage\Path,
     *     physical: non-empty-string,
     *     directory: bool
     * } Resolved directory entry.
     */
    private function resolveEntry (Path $directory, string $physical, string $name):array {

        if (AtomicWriter::isTemporary($name))
            throw new ReservedPathException(
                context: [
                    'directory' => $directory->value(),
                    'name' => $name
                ]
            );

        $candidate = $physical.Paths::DIRECTORY_SEPARATOR.$name;

        Runtime\FileSystem\Metadata::clearCache(true, $candidate);

        if (Runtime\FileSystem\Link::symbolicExists($candidate))
            throw new SymbolicLinkNotAllowedException(
                context: [
                    'directory' => $directory->value(),
                    'name' => $name
                ]
            );

        $path = $directory->join($name);
        $resolved = $this->resolver->resolve($path);

        Runtime\FileSystem\Metadata::clearCache(true, $resolved);

        $is_directory = Runtime\FileSystem\Folder::exists($resolved);

        if (!$is_directory && !Runtime\FileSystem\File::isFile($resolved))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $path->value()
                ]
            );

        return [
            'path' => $path,
            'physical' => $resolved,
            'directory' => $is_directory
        ];

    }

    /**
     * ### Validates a storage directory tree
     *
     * Recursively validates all descendant filesystem objects before performing a directory mutation.
     *
     * Validation does not modify filesystem objects and does not guarantee that the directory tree remains unchanged
     * during later operations.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locateDirectory() To resolve an existing physical
     * directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::entries() To enumerate physical directory
     * entries.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::resolveEntry() To validate and resolve
     * individual directory entries.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::validateTree() To recursively validate
     * descendant directories.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $directory <p>
     * Logical directory path to validate.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    private function validateTree (Path $directory):void {

        $physical = $this->resolver->locateDirectory($directory);

        foreach ($this->entries($physical) as $name) {

            $entry = $this->resolveEntry(
                $directory,
                $physical,
                $name
            );

            if ($entry['directory'])
                $this->validateTree($entry['path']);

        }

    }

    /**
     * ### Recursively deletes a storage directory tree
     *
     * Deletes descendant regular files and directories before deleting the specified directory itself.
     *
     * Filesystem entries are revalidated immediately before processing. Recursive deletion is not guaranteed to be
     * atomic.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locateDirectory() To resolve an existing physical
     * directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::entries() To enumerate physical directory
     * entries.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::resolveEntry() To validate and resolve
     * individual directory entries.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::deleteTree() To recursively delete descendant
     * directories.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::deleteFile() To delete descendant regular files.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::deleteDirectory() To delete the remaining
     * empty directory.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $directory <p>
     * Logical directory path to recursively delete.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    private function deleteTree (Path $directory):void {

        $physical = $this->resolver->locateDirectory($directory);

        foreach ($this->entries($physical) as $name) {

            $entry = $this->resolveEntry(
                $directory,
                $physical,
                $name
            );

            if ($entry['directory'])

                $this->deleteTree($entry['path']);

            else

                $this->deleteFile($entry['path']);

        }

        $this->deleteDirectory($directory);

    }

    /**
     * ### Deletes a descendant regular file
     *
     * Resolves an existing regular file and removes it from the physical filesystem during recursive directory
     * deletion.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locate() To safely resolve an existing regular file.
     * @uses \FireHub\Runtime\FileSystem\File::delete() To delete the physical file.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $path <p>
     * Logical path of the file to delete.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    private function deleteFile (Path $path):void {

        $physical = $this->resolver->locate($path);

        try {

            Runtime\FileSystem\File::delete($physical);

        } catch (FireHubException $exception) {

            throw new DirectoryDeletionException(
                context: [
                    'path' => $path->value()
                ],
                previous: $exception
            );

        } finally {

            Runtime\FileSystem\Metadata::clearCache(true, $physical);

        }

    }

    /**
     * ### Deletes an empty physical storage directory
     *
     * Resolves an existing physical directory and removes it from the filesystem.
     *
     * The physical directory must be empty.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locateDirectory() To safely resolve an existing
     * physical directory.
     * @uses \FireHub\Runtime\FileSystem\Folder::delete() To delete the physical directory.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $directory <p>
     * Logical path of the directory to delete.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    private function deleteDirectory (Path $directory):void {

        $physical = $this->resolver->locateDirectory($directory);

        try {

            Runtime\FileSystem\Folder::delete($physical);

        } catch (FireHubException $exception) {

            throw new DirectoryDeletionException(
                context: [
                    'path' => $directory->value()
                ],
                previous: $exception
            );

        } finally {

            Runtime\FileSystem\Metadata::clearCache(true, $physical);

        }

    }

    /**
     * ### Validates a directory transfer destination
     *
     * Validates the existence of the source directory, the availability of the destination path, and the
     * relationship between both paths.
     *
     * Returns false if both paths identify the same existing directory.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolve() To safely resolve physical directory paths.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::sameLocation() To determine whether both
     * paths identify the same location.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::isDescendant() To determine whether the
     * destination is inside the source directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::exists() To determine whether the destination
     * parent directory exists.
     * @uses \FireHub\Foundation\IO\Storage\Path::parent() To retrieve the logical destination parent directory.
     * @uses \FireHub\Runtime\FileSystem\Metadata::clearCache() To clear cached filesystem metadata.
     * @uses \FireHub\Runtime\FileSystem\File::exist() To determine whether a filesystem object exists.
     * @uses \FireHub\Runtime\FileSystem\Folder::exists() To determine whether a filesystem object is a directory.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $source <p>
     * Logical source directory path.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Path $destination <p>
     * Logical destination directory path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\ReservedPathException If the path segment is reserved.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return bool True if the directory transfer should proceed.
     */
    private function validateDestination (Path $source, Path $destination):bool {

        $source_physical = $this->resolver->resolve($source);

        Runtime\FileSystem\Metadata::clearCache(true, $source_physical);

        if (!Runtime\FileSystem\File::exist($source_physical))
            throw new DirectoryNotFoundException(
                context: [
                    'path' => $source->value()
                ]
            );

        if (!Runtime\FileSystem\Folder::exists($source_physical))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $source->value()
                ]
            );

        $destination_physical = $this->resolver->resolve($destination);

        Runtime\FileSystem\Metadata::clearCache(true, $destination_physical);

        if (
            Runtime\FileSystem\Folder::exists($destination_physical)
            && $this->sameLocation($source_physical, $destination_physical)
        ) return false;

        if ($this->isDescendant($source_physical, $destination_physical))
            throw new InvalidDirectoryDestinationException(
                context: [
                    'source' => $source->value(),
                    'destination' => $destination->value()
                ]
            );

        if (Runtime\FileSystem\Folder::exists($destination_physical))
            throw new DirectoryAlreadyExistsException(
                context: [
                    'path' => $destination->value()
                ]
            );

        if (Runtime\FileSystem\File::exist($destination_physical))
            throw new InvalidObjectTypeException(
                context: [
                    'path' => $destination->value()
                ]
            );

        $parent = $destination->parent();

        if (!$this->exists($parent))
            throw new DirectoryNotFoundException(
                context: [
                    'path' => $parent?->value()
                ]
            );

        return true;

    }

    /**
     * ### Determines whether physical paths identify the same location
     *
     * Compares resolved physical paths using platform-specific case comparison rules.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Compare::lexical() To compare resolved physical paths lexically.
     *
     * @param non-empty-string $source <p>
     * Physical source path.
     * </p>
     * @param non-empty-string $destination <p>
     * Physical destination path.
     * </p>
     *
     * @return bool True if both physical paths compare as identical.
     */
    private function sameLocation (string $source, string $destination):bool {

        return PlatformInfo::OS_FAMILY === 'Windows'
            ? Runtime\Str\SB\Compare::lexical($source, $destination, false) === 0
            : $source === $destination;

    }

    /**
     * ### Determines whether a physical path is a descendant
     *
     * Determines whether the destination physical path is located within the source directory hierarchy.
     *
     * Directory boundaries are included in the comparison to prevent unrelated paths with common prefixes from
     * matching.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Casing::toLower() To convert physical paths to lowercase for comparison.
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To compare physical path prefixes.
     *
     * @param non-empty-string $source <p>
     * Physical source directory path.
     * </p>
     * @param non-empty-string $destination <p>
     * Physical destination path.
     * </p>
     *
     * @return bool True if the destination is a descendant of the source.
     */
    private function isDescendant (string $source, string $destination):bool {

        $prefix = $source.Paths::DIRECTORY_SEPARATOR;

        if (PlatformInfo::OS_FAMILY === 'Windows') {

            $prefix = Runtime\Str\SB\Casing::toLower($prefix);

            $destination = Runtime\Str\SB\Casing::toLower($destination);

        }

        return Runtime\Str\SB\Search::startsWith($prefix, $destination);

    }

    /**
     * ### Recursively copies a storage directory tree
     *
     * Creates the destination directory before recursively copying its descendant regular files and directories.
     *
     * Empty directories are preserved. Source filesystem entries are revalidated before processing.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locateDirectory() To safely resolve the physical
     * source directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::createDirectory() To create the physical
     * destination directory.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::entries() To enumerate physical source
     * directory entries.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::resolveEntry() To validate and resolve source
     * directory entries.
     * @uses \FireHub\Foundation\IO\Storage\Path::join() To construct logical destination paths.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::copyTree() To recursively copy descendant
     * directories.
     * @uses \FireHub\Foundation\IO\Storage\Internal\DirectoryOperator::copyFile() To copy descendant regular files.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $source <p>
     * Logical source directory path.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Path $destination <p>
     * Logical destination directory path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    private function copyTree (Path $source, Path $destination):void {

        $physical = $this->resolver->locateDirectory($source);

        $this->createDirectory($destination);

        foreach ($this->entries($physical) as $name) {

            $entry = $this->resolveEntry(
                $source,
                $physical,
                $name
            );

            $target = $destination->join($name);

            if ($entry['directory']) $this->copyTree($entry['path'], $target);
            else $this->copyFile($entry['path'], $target);

        }

    }

    /**
     * ### Copies a descendant regular file
     *
     * Copies the contents of an existing regular file to a destination within the storage root directory.
     *
     * File contents are transferred through streams to avoid loading the entire source file into memory.
     *
     * The destination file uses the configured storage permission policy.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::locate() To safely resolve the existing source file.
     * @uses \FireHub\Foundation\IO\Storage\Internal\PathResolver::resolveFile() To safely resolve the destination file.
     * @uses \FireHub\Foundation\IO\Storage\Internal\AtomicWriter::write() To write the destination file through a
     * temporary file.
     * @uses \FireHub\Foundation\IO\Stream\Copier::copy() To transfer source file contents.
     * @uses \FireHub\Foundation\IO\Stream\File::close() To release the source file stream.
     *
     * @param \FireHub\Foundation\IO\Storage\Path $source <p>
     * Logical source file path.
     * </p>
     * @param \FireHub\Foundation\IO\Storage\Path $destination <p>
     * Logical destination file path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the logical path is invalid.
     * @throws \FireHub\Foundation\IO\Storage\Exception\SymbolicLinkNotAllowedException If a path component is a
     * symbolic link.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidObjectTypeException If an intermediate path component
     * is not a directory.
     * @throws \FireHub\Foundation\IO\Storage\Exception\PathOutsideRootException If the resolved path is outside the
     * storage root directory.
     * @throws \FireHub\Core\Exception\FireHubException If an existing filesystem path cannot be resolved.
     *
     * @return void
     */
    private function copyFile (Path $source, Path $destination):void {

        $source_physical = $this->resolver->locate($source);
        $destination_physical = $this->resolver->resolveFile($destination);

        try {

            AtomicWriter::write(
                $destination_physical,
                static function (Writable $stream) use ($source_physical):void {

                    $file = new File(
                        $source_physical,
                        Runtime\Stream\Enum\OpenMode::READ_BINARY
                    );

                    try {

                        Copier::copy($file, $stream);

                    } finally {

                        $file->close();

                    }

                },
                $this->permissions
            );

        } catch (FireHubException $exception) {

            throw new DirectoryCopyException(
                context: [
                    'source' => $source->value(),
                    'destination' => $destination->value()
                ],
                previous: $exception
            );

        }

    }

}