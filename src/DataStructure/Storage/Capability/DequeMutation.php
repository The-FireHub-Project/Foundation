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

namespace FireHub\Foundation\DataStructure\Storage\Capability;

/**
 * ### Defines the ability to mutate both boundaries of a storage
 *
 * Deque mutation combines front mutation and back mutation into a single capability. It provides the complete set
 * of insertion and removal operations available at both boundaries of a storage.
 *
 * The capability allows values to be inserted or removed from either the front or back boundary while leaving the
 * underlying storage representation and organization to the implementing storage.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @extends \FireHub\Foundation\DataStructure\Storage\Capability\FrontMutation<TValue>
 * @extends \FireHub\Foundation\DataStructure\Storage\Capability\BackMutation<TValue>
 */
interface DequeMutation extends FrontMutation, BackMutation {}