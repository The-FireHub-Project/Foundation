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

namespace FireHub\Foundation\State;

/**
 * ### Manages shared state
 *
 * Shared state provides a container for an underlying state that may be shared between multiple owners.
 *
 * It tracks the number of owners currently referencing the state, allowing consumers to determine whether the
 * underlying state is shared and must be detached before mutation. This enables copy-on-write behavior without
 * prescribing the type, structure, or copying strategy of the managed state.
 *
 * The shared state manages ownership metadata only. Consumers remain responsible for creating, copying, detaching,
 * and mutating their underlying state.
 * @since 1.0.0
 *
 * @template TData
 */
final class SharedState {

    /**
     * ### Number of owners
     *
     * Number of owners currently sharing the underlying state.
     * @since 1.0.0
     *
     * @var positive-int
     */
    private int $owners = 1;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TData $data <p>
     * The underlying data shared between its owners.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private mixed $data
    ) {}

    /**
     * ### Gets the underlying data
     *
     * Returns a reference to the underlying data managed by this shared state.
     * @since 1.0.0
     *
     * @return TData The underlying data.
     */
    public function &data ():mixed {

        return $this->data;

    }

    /**
     * ### Gets the number of owners
     *
     * Returns the number of owners currently sharing the underlying state.
     * @since 1.0.0
     *
     * @return positive-int Number of owners.
     */
    public function owners ():int {

        return $this->owners;

    }

    /**
     * ### Determines whether the state is shared
     *
     * Determines whether more than one owner currently references the underlying state.
     * @since 1.0.0
     *
     * @return bool True if the state is shared, otherwise false.
     */
    public function isShared ():bool {

        return $this->owners > 1;

    }

    /**
     * ### Determines whether the state is exclusive
     *
     * Determines whether only one owner currently references the underlying state.
     * @since 1.0.0
     *
     * @return bool True if the state is exclusive, otherwise false.
     */
    public function isExclusive ():bool {

        return $this->owners === 1;

    }

    /**
     * ### Acquires ownership of the state
     *
     * Registers another owner of the underlying state.
     * @since 1.0.0
     *
     * @return void
     */
    public function acquire ():void {

        $this->owners++;

    }

    /**
     * ### Releases ownership of the state
     *
     * Removes one owner from the underlying state.
     * @since 1.0.0
     *
     * @return void
     */
    public function release ():void {

        if ($this->owners > 1)
            $this->owners--;

    }

}