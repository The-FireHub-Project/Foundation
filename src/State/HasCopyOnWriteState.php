<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation
 */

namespace FireHub\Foundation\State;

/**
 * ### Provides copy-on-write state management
 *
 * Provides reusable state management for objects that support copy-on-write semantics.
 *
 * The trait maintains a shared state that may be referenced by multiple owners and provides the mechanism for
 * detaching that state before mutation. When the state is shared, detachment creates an independent state for the
 * current owner, allowing later mutations without affecting other owners.
 *
 * Concrete implementations remain responsible for defining how the underlying state data is copied during
 * detachment.
 * @since 1.0.0
 *
 * @template TData
 */
trait HasCopyOnWriteState {

    /**
     * ### The shared state
     * @since 1.0.0
     *
     * @var \FireHub\Foundation\State\SharedState<TData>
     */
    protected SharedState $state;

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\HasCopyOnWriteState::copyData() To copy the data for detachment.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data.
     */
    public function copy ():static {

        return clone($this, [
            'state' => new SharedState(
                $this->copyData($this->state->data())
            )
        ]);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::acquire() To acquire the shared state.
     */
    public function fork ():static {

        $this->state->acquire();

        return clone($this, [
            'state' => $this->state
        ]);

    }

    /**
     * ### Detaches the shared state
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::release() To release the shared state.
     * @uses \FireHub\Foundation\State\SharedState::isShared() To check if the state is shared.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data.
     * @uses \FireHub\Foundation\State\HasCopyOnWriteState::copyData() To copy the data for detachment.
     *
     * @return void
     */
    protected function detach ():void {

        if (!$this->state->isShared()) return;

        $state = $this->state;

        $state->release();

        $this->state = new SharedState(
            $this->copyData($state->data())
        );

    }

    /**
     * ### Copies the data for detachment
     * @since 1.0.0
     *
     * @param TData $data <p>
     * The data to copy.
     * </p>
     *
     * @return TData Returns a copy of the data.
     */
    abstract protected function copyData (mixed $data):mixed;

}