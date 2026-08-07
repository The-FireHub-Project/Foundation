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

namespace FireHub\Foundation\Str\Operation;

use FireHub\Foundation\Str\Base;
use FireHub\Runtime;

/**
 * ### Sanitizes string content by removing or normalizing unsafe elements
 *
 * This class provides methods for cleaning string values by removing unwanted or potentially unsafe content.
 *
 * It is intended for preparing strings before display or further processing by stripping elements that do not
 * belong to the intended output format.
 *
 * The class keeps sanitization concerns separate from general string transformations and escaping operations.
 * @since 1.0.0
 *
 * @template TBase of \FireHub\Foundation\Str\Base
 */
final readonly class Sanitize {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param TBase $base <p>
     * The string to extract from.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Base $base
    ) {}

    /**
     * ### Removes HTML tags from the string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Sanitaze;
     * use FireHub\Foundation\Str;
     *
     * $string = new Sanitize(
     *  new Str('<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>')
     * )->stripTags();
     *
     * // 'Test paragraph. Other text'
     *
     * $string = new Sanitize(
     *  new Str('<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>')
     * )->stripTags(['p', 'a']);
     *
     * // '<p>Test paragraph.</p> <a href="#fragment">Other text</a>'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Escape::stripTags() To remove HTML tags from the string.
     * @uses \FireHub\Foundation\Str\Boundary\Sanitizable::value() To get the string value.
     * @uses \FireHub\Foundation\Str\Boundary\Sanitizable::encoding() To get the string encoding.
     *
     * @param null|string|array<int, string> $allowed_tags <p>
     * You can use the optional second parameter to specify tags which shouldn't be stripped.
     * </p>
     *
     * @return TBase Returns a new instance of the string with all HTML tags removed.
     *
     * @note Self-closing XHTML tags are ignored, and only non-self-closing tags should be used in allowed_tags. For
     * example, to allow both ```<br>``` and ```<br/>```, you should use: ```<br>```.
     */
    public function stripTags (null|string|array $allowed_tags = null):Base {

        return new $this->base(
            Runtime\Str\SB\Escape::stripTags($this->base->value(), $allowed_tags),
            $this->base->encoding()
        );

    }

}