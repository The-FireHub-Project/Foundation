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

namespace FireHub\Foundation\DataStructure\Storage\Initialization;

use FireHub\Foundation\DataStructure\Storage\Initializer;
use Generator;

/**
 * ### Provides an initialization strategy with no initial contents
 *
 * Empty initializer provides an empty iterator as the initial contents of a storage. It is used when a storage
 * should be created without any initial values while preserving the common initializer contract.
 *
 * The initializer does not impose any storage representation or behavior on the consuming storage.
 * @since 1.0.0
 *
 * @implements \FireHub\Foundation\DataStructure\Storage\Initializer<mixed, mixed>
 */
final readonly class EmptyInit implements Initializer {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @return Generator<mixed, mixed> The generator yielding no values, representing the empty initial contents of
     * the storage.
     */
    public function initialize ():Generator {

        yield from [];

    }

}