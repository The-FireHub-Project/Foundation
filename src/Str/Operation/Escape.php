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
 * ### Escapes and unescapes string content safely
 *
 * This class provides methods for escaping and restoring special characters in strings.
 *
 * It handles transformations required when preparing string values for specific contexts, such as escaping
 * slashes, quoting special characters, and making strings safe for regular expression usage.
 *
 * The class keeps escaping concerns isolated from general string transformations, allowing string values to be
 * adapted safely for external processing without changing their original meaning.
 * @since 1.0.0
 *
 * @template TBase of \FireHub\Foundation\Str\Base
 */
final readonly class Escape {

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
     * ### Adds slashes to the string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Escape;
     * use FireHub\Foundation\Str;
     *
     * $string = new Escape(new Str("O'Reilly"))->addSlashes();
     *
     * // "O\'Reilly"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Transform::flip() To flip the array keys and values.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Base::split() To split the string into an array.
     *
     * @param array<int, string> $characters [optional] <p>
     * The characters to escape.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException If the $length parameter is less than 1.
     *
     * @return TBase Returns a new instance of the string with slashes escaped.
     */
    public function addSlashes (array $characters = ["'", '"', "\\"]):Base {

        $escape = Runtime\Arr\Transform::flip($characters);

        $result = '';

        foreach ($this->base->split() as $character) {

            if (isset($escape[$character])) $result .= '\\';

            $result .= $character;

        }

        return new $this->base($result, $this->base->encoding());

    }

    /**
     * ### Removes slashes to the string
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Escape;
     * use FireHub\Foundation\Str;
     *
     * $string = new Escape(new Str("O\'Reilly"))->removeSlashes();
     *
     * // "O'Reilly"
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Transform::flip() To flip the array keys and values.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Base::split() To split the string into an array.
     *
     * @param array<int, string> $characters [optional] <p>
     * The characters to escape.
     * </p>
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException If the $length parameter is less than 1.
     *
     * @return TBase Returns a new instance of the string with slashes removed.
     */
    public function removeSlashes (array $characters = ["'", '"', "\\"]):Base {

        $characters = Runtime\Arr\Transform::flip($characters);

        $result = ''; $escaped = false;
        foreach ($this->base->split() as $character) {

            if ($escaped) {

                if (isset($characters[$character])) $result .= $character;
                else $result .= '\\'.$character;

                $escaped = false;

                continue;

            }

            if ($character === '\\') {

                $escaped = true;

                continue;

            }

            $result .= $character;

        }

        return new $this->base(
            $result,
            $this->base->encoding()
        );

    }

    /**
     * ### Quotes meta-characters in the string
     *
     * Returns a version of str with a backslash character (\) before every character that is among these: .\+*?[^]($).
     *
     * <code>
     * use FireHub\Foundation\Str\Operation\Escape;
     * use FireHub\Foundation\Str;
     *
     * $string = new Escape(new Str('FireHub.*'))->quoteMeta();
     *
     * // 'FireHub\.\*'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Escape::quoteMeta() To quote meta characters in the string.
     * @uses \FireHub\Foundation\Str\Base::encoding() To get the string encoding.
     * @uses \FireHub\Foundation\Str\Base::value() To get the string value.
     *
     * @return TBase Returns a new instance of the string with meta characters quoted.
     */
    public function quoteMeta ():Base {

        return new $this->base(
            Runtime\Str\SB\Escape::quoteMeta($this->base->value()),
            $this->base->encoding()
        );

    }

}