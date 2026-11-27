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

namespace FireHub\Foundation\IO\Storage;

use FireHub\Core\Type\FileSystem\PermissionMode;
use FireHub\Core\Meta\Enum\FileSystem\Permission;

/**
 * ### Storage Permissions
 *
 * Defines the filesystem permission policy applied to files and directories created within a storage disk.
 *
 * Existing standard permission bits may optionally be preserved when replacing stored files.
 *
 * Permission behavior depends on the underlying operating system and filesystem.
 *
 * <code>
 * $permissions = new Permissions(
 *     file: new PermissionMode(
 *         Permission::READ_WRITE,
 *         Permission::READ,
 *         Permission::NONE
 *     ),
 *     directory: new PermissionMode(
 *         Permission::ALL,
 *         Permission::READ_EXECUTE,
 *         Permission::NONE
 *     ),
 *     preserve_existing: true
 * );
 * </code>
 *
 * @since 1.0.0
 */
final readonly class Permissions {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\FileSystem\PermissionMode $file [optional] <p>
     * Default permission mode applied to newly created files.
     *
     * Defaults to 0600.
     * </p>
     * @param \FireHub\Core\Type\FileSystem\PermissionMode $directory [optional] <p>
     * Default permission mode applied to newly created directories.
     *
     * Defaults to 0700.
     * </p>
     * @param bool $preserve_existing [optional] <p>
     * Whether standard permission bits of existing files should be preserved when replacing their contents.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private PermissionMode $file = new PermissionMode(
            Permission::READ_WRITE,
            Permission::NONE,
            Permission::NONE
        ),
        private PermissionMode $directory = new PermissionMode(
            Permission::ALL,
            Permission::NONE,
            Permission::NONE
        ),
        private bool $preserve_existing = true
    ) {}

    /**
     * ### Gets the default file permission mode
     *
     * <code>
     * $permissions = new Permissions;
     *
     * $permissions->file()->octal();
     * // '0600'
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\FileSystem\PermissionMode Default file permission mode.
     */
    public function file ():PermissionMode {

        return $this->file;

    }

    /**
     * ### Gets the default directory permission mode
     *
     * <code>
     * $permissions = new Permissions;
     *
     * $permissions->directory()->octal();
     * // '0700'
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Core\Type\FileSystem\PermissionMode Default directory permission mode.
     */
    public function directory ():PermissionMode {

        return $this->directory;

    }

    /**
     * ### Determines whether existing file permissions should be preserved
     *
     * Only standard owner, group, and other permission bits are preserved.
     *
     * This policy does not preserve file ownership, access control lists, and special permission bits.
     *
     * <code>
     * $permissions = new Permissions;
     *
     * $permissions->preserveExisting();
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if existing standard file permissions should be preserved.
     */
    public function preserveExisting ():bool {

        return $this->preserve_existing;

    }

}