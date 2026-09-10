<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Foundation
 */

namespace FireHub\Foundation\Str\Trait;

use FireHub\Core\Type\Str\Encoding;
use FireHub\Foundation\Conversion\Policy\Strict;
use FireHub\Runtime;
use Stringable;

/**
 * ### Provides common instance creation capabilities
 *
 * Defines a reusable implementation for creating immutable value object instances from raw values.
 *
 * This trait provides standardized construction behavior, including direct instantiation and factory-based creation,
 * ensuring consistent initialization across compatible value object implementations.
 * @since 1.0.0
 *
 * @template TValue of string
 */
trait Instantiable {

    /**
     * ### Creates a new string instance from a given value
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::of('FireHub');
     *
     * // FireHub
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Conversion\Policy\Strict::string() To convert the value to a string.
     *
     * @param mixed $value <p>
     * The value to convert to a string.
     * </p>
     * @param \FireHub\Core\Type\Str\Encoding $encoding [optional] <p>
     * The encoding of the string.
     * </p>
     *
     * @throws \FireHub\Foundation\Conversion\Exception\ConversionException If the conversion fails.
     *
     * @return static<string> A new Str instance representing the string value.
     */
    public static function of (mixed $value, Encoding $encoding = self::DEFAULT_ENCODING):static {

        return new static(new Strict($value)->string(), $encoding);

    }

    /**
     * ### Concatenates multiple string values into a single string
     *
     * <code>
     * use FireHub\Foundation\Str;
     *
     * $string = Str::join(['a', 'b', 'c'], '-');
     *
     * // a-b-c
     *
     * $string = Str::join(['Fire', 'Hub'], ' ', 'and');
     *
     * // Fire and Hub
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To concatenate the values.
     * @uses \FireHub\Runtime\Arr\Mutation::pop() To remove the last value from the array.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To check if there are more than one value.
     *
     * @param iterable<mixed, null|scalar|Stringable> $values <p>
     * The values to concatenate.
     * </p>
     * @param string $separator [optional] <p>
     * The separator to use between the values.
     * </p>
     * @param null|string $conjunction [optional] <p>
     * The conjunction to use after the last value.
     * </p>
     * @param \FireHub\Core\Type\Str\Encoding $encoding [optional] <p>
     * The encoding of the string.
     * </p>
     *
     * @return static<string> A new Str instance representing the concatenated string values.
     */
    public static function join (iterable $values, string $separator = '', ?string $conjunction = null, Encoding $encoding = self::DEFAULT_ENCODING):static {

        $items = [];
        foreach ($values as $value) $items[] = (string)$value;

        if ($conjunction !== null && Runtime\Arr\Inspection::count($items) > 1) {

            $last = Runtime\Arr\Mutation::pop($items);

            return new static(
                Runtime\Str\SB\Delimiter::implode($items, $separator)
                .$separator
                .$conjunction
                .$separator
                .$last,
                $encoding
            );

        }

        return new static(Runtime\Str\SB\Delimiter::implode($items, $separator), $encoding);

    }

}