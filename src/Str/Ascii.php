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

namespace FireHub\Foundation\Str;

use FireHub\Foundation\Str\Trait\Instantiable;
use FireHub\Foundation\Str\Boundary\ {
    CaseTransformable, Patternable
};
use FireHub\Foundation\Str\Case\Converter;
use FireHub\Core\Type\Str\Encoding;

/**
 * ### Represents an ASCII encoded string value object
 *
 * Provides an immutable string value object that enforces ASCII character encoding semantics.
 *
 * This class ensures that the represented string value contains only characters supported by the ASCII encoding
 * while providing the common string behavior inherited from the base string value object implementation.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Foundation\Str\Base<TValue>
 */
readonly class Ascii extends Base implements CaseTransformable, Patternable {

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function __construct (string $value, Encoding $encoding = self::DEFAULT_ENCODING) {

        parent::__construct($value, $encoding);

        //var_dump($this->pattern()->match()->ascii());

    }

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
    public function toCase ():Converter {

        return new Converter($this);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function pattern ():Pattern {

        return new Pattern($this);

    }

}