<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.0
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure;

use FireHub\Core\Boundary\Capability\Iteration;

/**
 * ### Defines the fundamental contract for a data structure storage
 *
 * A storage provides the underlying mechanism used by a data structure to organize, retain, and access its
 * values. It abstracts the physical representation of stored data from the data structure that consumes it,
 * allowing different storage strategies to be used without changing the higher-level data structure contract.
 *
 * The storage contract defines only the operations that are common to all storage implementations. Specialized
 * storage contracts may extend this contract to provide capabilities for indexed access, keyed access, mutation,
 * boundary operations, node manipulation, metrics, or other storage-specific behavior.
 *
 * Storage is an implementation-level abstraction and does not define the public behavior of the data structure
 * that uses it.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @extends \FireHub\Core\Boundary\Capability\Iteration<TKey, TValue>
 */
interface Storage extends Iteration {}