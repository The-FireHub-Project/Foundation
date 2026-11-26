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

namespace FireHub\Foundation\IO;

use FireHub\Core\Boundary\IO\ {
    Storage\Disk as DiskContract, Storage as StorageContract
};
use FireHub\Foundation\IO\Storage\Name;
use FireHub\Foundation\IO\Storage\Exception\ {
    DefaultDiskNotConfiguredException, DiskAlreadyExistsException, DiskNotFoundException, StorageFrozenException
};
use FireHub\Runtime;

/**
 * ### Storage registry
 *
 * Provides centralized registration, configuration, and retrieval of named storage disks.
 *
 * Each registered disk represents an independently configured storage resource.
 *
 * The registry may be frozen to prevent configuration changes while preserving access to registered disks.
 *
 * <code>
 * $storage = new Storage;
 *
 * $storage->register(
 *     'local',
 *     new Storage\Disk(new Storage\Driver\Local(__DIR__.'/storage'))
 * );
 *
 * $storage->setDefault('local');
 * $storage->freeze();
 *
 * $storage->disk()->put('example.txt', 'Contents');
 * </code>
 *
 * @since 1.0.0
 */
final class Storage implements StorageContract {

    /**
     * ### Registered storage disks
     * @since 1.0.0
     *
     * @var array<non-empty-string, \FireHub\Core\Boundary\IO\Storage\Disk>
     */
    private array $disks = [];

    /**
     * ### Default storage disk name
     * @since 1.0.0
     */
    private ?Name $default = null;

    /**
     * ### Storage registry frozen state
     * @since 1.0.0
     */
    private bool $frozen = false;

    /**
     * ### Registers a storage disk
     *
     * Associates a storage disk with a unique name.
     *
     * Existing disk registrations cannot be overwritten.
     *
     * <code>
     * $storage->register(
     *     'memory',
     *     new Storage\Disk(new Storage\Driver\Memory)
     * );
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage::ensureMutable() To ensure the registry is not frozen.
     * @uses \FireHub\Foundation\IO\Storage\Name::value() To retrieve the validated disk name.
     *
     * @param non-empty-string $name <p>
     * Unique storage disk name.
     * </p>
     * @param \FireHub\Core\Boundary\IO\Storage\Disk $disk <p>
     * Storage disk to register.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\StorageFrozenException If the registry is frozen.
     * @throws \FireHub\Foundation\IO\Storage\Exception\DiskAlreadyExistsException If the disk is already registered.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidDiskNameException If the disk name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function register (string $name, DiskContract $disk):void {

        $this->ensureMutable();

        $name = new Name($name)->value();

        if (isset($this->disks[$name]))
            throw new DiskAlreadyExistsException;

        $this->disks[$name] = $disk;

    }

    /**
     * ### Unregisters a storage disk
     *
     * Removes a registered storage disk without deleting its stored objects.
     *
     * If the removed disk is configured as the default, the default configuration is cleared.
     *
     * <code>
     * $storage->unregister('memory');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage::ensureMutable() To ensure the registry is not frozen.
     * @uses \FireHub\Foundation\IO\Storage\Name::value() To retrieve the default disk name.
     *
     * @param string $name <p>
     * Name of the storage disk to unregister.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\StorageFrozenException If the registry is frozen.
     * @throws \FireHub\Foundation\IO\Storage\Exception\DiskNotFoundException If the disk is not registered.
     *
     * @return void
     */
    public function unregister (string $name):void {

        $this->ensureMutable();

        if (!isset($this->disks[$name]))
            throw new DiskNotFoundException;

        unset($this->disks[$name]);

        if ($this->default?->value() === $name)
            $this->default = null;

    }

    /**
     * ### Gets a registered storage disk
     *
     * Retrieves a storage disk by its registered name.
     *
     * If no name is provided, the configured default storage disk is returned.
     *
     * <code>
     * $disk = $storage->disk('local');
     *
     * $default = $storage->disk();
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Name::value() To retrieve the default disk name.
     *
     * @param null|non-empty-string $name [optional] <p>
     * Registered storage disk name or null to retrieve the default disk.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\DefaultDiskNotConfiguredException If no default disk is configured.
     * @throws \FireHub\Foundation\IO\Storage\Exception\DiskNotFoundException If the requested disk is not registered.
     *
     * @return \FireHub\Core\Boundary\IO\Storage\Disk Requested storage disk.
     */
    public function disk (?string $name = null):DiskContract {

        $name ??= $this->default?->value()
            ?? throw new DefaultDiskNotConfiguredException;

        return $this->disks[$name]
            ?? throw new DiskNotFoundException;

    }

    /**
     * ### Determines whether a storage disk is registered
     *
     * <code>
     * $storage->has('local');
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $name <p>
     * Storage disk name.
     * </p>
     *
     * @return bool True if the storage disk is registered, false otherwise.
     */
    public function has (string $name):bool {

        return isset($this->disks[$name]);

    }

    /**
     * ### Configures the default storage disk
     *
     * The specified storage disk must already be registered.
     *
     * <code>
     * $storage->setDefault('local');
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage::ensureMutable() To ensure the registry is not frozen.
     * @uses \FireHub\Foundation\IO\Storage\Name::value() To retrieve the validated disk name.
     *
     * @param non-empty-string $name <p>
     * Name of the registered storage disk.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\StorageFrozenException If the registry is frozen.
     * @throws \FireHub\Foundation\IO\Storage\Exception\DiskNotFoundException If the disk is not registered.
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidDiskNameException If the disk name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function setDefault (string $name):void {

        $this->ensureMutable();

        $storage_name = new Name($name);

        if (!isset($this->disks[$storage_name->value()]))
            throw new DiskNotFoundException;

        $this->default = $storage_name;

    }

    /**
     * ### Gets the default storage disk name
     *
     * <code>
     * $storage->default();
     * // 'local'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Name::value() To retrieve the default disk name.
     *
     * @return null|non-empty-string Default storage disk name or null if no default disk is configured.
     */
    public function default ():?string {

        return $this->default?->value();

    }

    /**
     * ### Gets the registered storage disk names
     *
     * Returns disk names in their registration order.
     *
     * <code>
     * $storage->names();
     * // ['local', 'memory', 'views']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Access::keys() To retrieve the registered disk names.
     *
     * @return list<non-empty-string> Registered storage disk names.
     */
    public function names ():array {

        return Runtime\Arr\Access::keys($this->disks);

    }

    /**
     * ### Freezes the storage registry
     *
     * Prevents further registration, unregistration, and default disk configuration.
     *
     * Operations on registered storage disks remain available.
     *
     * Repeated calls have no additional effect.
     *
     * <code>
     * $storage->freeze();
     * </code>
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function freeze ():void {

        $this->frozen = true;

    }

    /**
     * ### Determines whether the storage registry is frozen
     *
     * <code>
     * $storage->freeze();
     * $storage->isFrozen();
     * // true
     * </code>
     *
     * @since 1.0.0
     *
     * @return bool True if the storage registry is frozen, false otherwise.
     */
    public function isFrozen ():bool {

        return $this->frozen;

    }

    /**
     * ### Ensures that the storage registry is mutable
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\StorageFrozenException If the registry is frozen.
     *
     * @return void
     */
    private function ensureMutable ():void {

        if ($this->frozen)
            throw new StorageFrozenException;

    }

}