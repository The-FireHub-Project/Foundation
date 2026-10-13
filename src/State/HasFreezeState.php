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

namespace FireHub\Foundation\State;

use FireHub\Foundation\State\Exception\FrozenStateException;

/**
 * ### Provides freeze state management
 *
 * Provides reusable state management for objects that support freezing semantics.
 *
 * The trait maintains whether the current instance is frozen and provides a guard for operations that require
 * a mutable state. Once frozen, the instance cannot transition back to a mutable state.
 * @since 1.0.0
 */
trait HasFreezeState {

    /**
     * ### Whether the instance is frozen
     * @since 1.0.0
     */
    protected bool $frozen = false;

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function isFrozen ():bool {

        return $this->frozen;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function freeze ():static {

        $this->frozen = true;

        return $this;

    }

    /**
     * ### Marks this instance as mutable
     *
     * Resets the freeze state of this instance, allowing mutation operations.
     *
     * This method is intended for internal lifecycle operations such as thawing and must not be exposed as a public
     * mutability operation.
     * @since 1.0.0
     *
     * @return void
     */
    protected function thawState ():void {

        $this->frozen = false;

    }

    /**
     * ### Guards mutable state
     *
     * Ensures that the current instance may be mutated.
     * @since 1.0.0
     *
     * @throws \FireHub\Foundation\State\Exception\FrozenStateException If the instance is frozen.
     *
     * @return void
     */
    protected function guardMutable ():void {

        if ($this->frozen)
            throw new FrozenStateException;

    }

}