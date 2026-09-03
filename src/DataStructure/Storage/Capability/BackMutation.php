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
 * ### Defines the ability to mutate the back of a storage
 *
 * Back mutation combines back insertion and back removal into a single capability. It provides the complete set of
 * mutation operations available at the back boundary of a storage.
 *
 * The capability does not define how values are represented, organized, or stored internally. It only defines the
 * operations available for modifying the back boundary.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @extends \FireHub\Foundation\DataStructure\Storage\Capability\BackInsertion<TValue>
 * @extends \FireHub\Foundation\DataStructure\Storage\Capability\BackRemoval<TValue>
 */
interface BackMutation extends BackInsertion, BackRemoval {}