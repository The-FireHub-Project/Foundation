<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.4
 * @package Foundation
 */

namespace FireHub\Foundation;

use FireHub\Core\Type\Str\Encoding;

/**
 * ### Provides the base implementation for string-based Value Objects
 *
 * The StringValue class represents the common foundation for string-based Value Objects within the FireHub Foundation
 * layer.
 *
 * It provides shared immutable value semantics, encoding management, and string representation behavior used by
 * concrete string types such as Str and Char.
 *
 * This class centralizes common string value handling while allowing specialized implementations to define their own
 * domain-specific behavior and operations.
 * @since 1.0.0
 *
 * @template TValue of string
 */
trait StringValue {

    /**
     * ### Default encoding
     * @since 1.0.0
     */
    protected const Encoding DEFAULT_ENCODING = Encoding::UTF_8;

    /**
     * ### Creates a new Convert instance for the current string value
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('123')->convert()->strict()->int();
     *
     * // 123
     * </code>
     *
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\Convert Returns a new instance of the Convert class.
     */
    public function convert ():Convert {

        return new Convert($this->value);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub')->value();
     *
     * // FireHub
     * </code>
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $encoding = Str::of('FireHub')->encoding();
     *
     * // Encoding::UTF_8
     * </code>
     *
     * @uses \FireHub\Runtime\Str\MB\Configuration::encoding() To get the default encoding.
     *
     * @since 1.0.0
     *
     */
    public function encoding ():Encoding {

        return $this->encoding;

    }

    /**
     * ### Returns a new instance with the specified encoding
     *
     * <code>
     * use FireHub\Foundation\Str;
     * use FireHub\Core\Type\Str\Encoding;
     *
     * $string = Str::of('FireHub')->withEncoding(Encoding::ASCII);
     *
     * $encoding = $string->encoding();
     *
     * // Encoding::ASCII
     * </code>
     *
     * @since 1.0.0
     *
     * @param \FireHub\Core\Type\Str\Encoding $encoding<p>
     * The encoding to set.
     * </p>
     *
     * @return static The new instance with provided encoding.
     */
    public function withEncoding (Encoding $encoding):static {

        return new static($this->value, $encoding);

    }

}