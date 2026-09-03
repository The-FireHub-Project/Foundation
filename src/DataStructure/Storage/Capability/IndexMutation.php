<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.1
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure\Storage\Capability;

use FireHub\Core\Meta\Enum\MutationOutcome;

/**
 * ### Defines indexed mutation of values in a storage
 *
 * Indexed mutation provides the ability to create, update, and remove values at specific integer indexes. Each
 * mutation operation reports its outcome through a MutationOutcome value.
 * @since 1.0.0
 *
 * @template TValue
 */
interface IndexMutation {

    /**
     * ### Sets the value at the specified index
     *
     * Stores the specified value at the given index. If the index does not exist, a new entry is created. If the
     * index already exists, its value is replaced.
     * @since 1.0.0
     *
     * @param int $index <p>
     * The index at which to store the value.
     * </p>
     *
     * @param TValue $value <p>
     * The value to store.
     * </p>
     *
     * @return \FireHub\Core\Meta\Enum\MutationOutcome The outcome of the mutation: CREATED if a new entry was
     * created, or UPDATED if an existing entry was updated.
     */
    public function set (int $index, mixed $value):MutationOutcome;

    /**
     * ### Removes the value at the specified index
     *
     * Removes the value currently stored at the specified index. If the index does not exist, no mutation is
     * performed.
     * @since 1.0.0
     *
     * @param int $index <p>
     * The index of the value to remove.
     * </p>
     *
     * @return \FireHub\Core\Meta\Enum\MutationOutcome The outcome of the mutation: REMOVED if an entry was removed,
     * or NOT_FOUND if the index does not exist.
     */
    public function remove (int $index):MutationOutcome;

}