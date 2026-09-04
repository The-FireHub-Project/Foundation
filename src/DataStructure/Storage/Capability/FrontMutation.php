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
 * ### Defines the ability to mutate the front of a storage
 *
 * Front mutation combines front insertion and front removal into a single capability. It provides the complete set
 * of mutation operations available at the front boundary of a storage.
 *
 * The capability does not define how values are represented, organized, or stored internally. It only defines the
 * operations available for modifying the front boundary.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @extends \FireHub\Foundation\DataStructure\Storage\Capability\FrontInsertion<TValue>
 * @extends \FireHub\Foundation\DataStructure\Storage\Capability\FrontRemoval<TValue>
 */
interface FrontMutation extends FrontInsertion, FrontRemoval {}