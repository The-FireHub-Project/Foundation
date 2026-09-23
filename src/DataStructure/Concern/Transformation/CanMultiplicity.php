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

namespace FireHub\Foundation\DataStructure\Concern\Transformation;

use FireHub\Foundation\DataStructure\Transformation\Multiplicity;

/**
 * ### Provides multiplicity transformation capabilities
 *
 * Provides access to higher-level multiplicity transformations for values contained within the canonical iteration
 * sequence of a data structure.
 *
 * This trait exposes transformations for selecting distinct, unique, and duplicate values without defining or
 * altering the underlying data structure, storage, or iteration semantics.
 * @since 1.0.0
 *
 * @template TKey
 * @template TValue
 *
 * @phpstan-require-implements \FireHub\Core\Boundary\Capability\Transformation\Filterable<TKey, TValue>
 */
trait CanMultiplicity {

    /**
     * ### Creates a multiplicity transformation instance
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Transformation\Multiplicity<TKey, TValue, $this> The multiplicity
     * transformation.
     */
    public function multiplicity ():Multiplicity {

        /** @var \FireHub\Foundation\DataStructure\Transformation\Multiplicity<TKey, TValue, $this> */
        return new Multiplicity($this);

    }

}