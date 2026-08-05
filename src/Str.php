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

namespace FireHub\Foundation;

use FireHub\Foundation\Str\Base;
use FireHub\Foundation\Str\Trait\Instantiable;
use FireHub\Foundation\Str\Boundary\ {
    Caseable, CaseTransformable, Patternable
};
use FireHub\Foundation\Str\Case\ {
    Casing, Converter
};
use FireHub\Foundation\Str\Pattern;
use FireHub\Runtime\Type\Str\ {
    RegexDelimiter, RegexFlag
};

/**
 * ### Provides an immutable string value object with a high-level developer API
 *
 * The Str class represents a string value within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with strings while preserving immutable value
 * semantics inherited from the Core Value Object system.
 *
 * The class implements the Core string type contract and extends the base Value Object abstraction, allowing string
 * values to be used consistently across the FireHub ecosystem.
 *
 * This class is responsible for high-level string operations and developer experience, while low-level string
 * execution remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Foundation\Str\Base<TValue>
 */
readonly class Str extends Base implements Caseable, CaseTransformable, Patternable {

    /**
     * ### Instantiable
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\Str\Trait\Instantiable<TValue>
     */
    use Instantiable;

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function case ():Casing {

        return new Casing($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function transform ():Converter {

        return new Converter($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function pattern (RegexDelimiter $delimiter = RegexDelimiter::SLASH, RegexFlag ...$flags):Pattern {

        return new Pattern($this, $delimiter, ...$flags);

    }

}