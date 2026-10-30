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

namespace FireHub\Foundation\Uri\Encoding;

use FireHub\Runtime;

/**
 * ### URI percent encoder
 *
 * Provides component-specific percent-encoding and decoding according to RFC 3986.
 *
 * Encoding preserves characters allowed by the target URI component and percent-encodes all other bytes.
 *
 * Input values are treated as decoded data. Existing percent signs are encoded rather than interpreted as escape
 * sequences.
 * @since 1.0.0
 */
final readonly class Percent {

    /**
     * ### Encodes a URI path
     *
     * Preserves forward slashes as path separators.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\Percent::encode() To percent-encode disallowed bytes.
     *
     * @param string $value <p>
     * Decoded URI path.
     * </p>
     *
     * @return string Percent-encoded URI path.
     */
    public static function encodePath (string $value):string {

        return self::encode(
            $value,
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~!$&'()*+,;=:@/"
        );

    }

    /**
     * ### Encodes a URI query
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\Percent::encode() To percent-encode disallowed bytes.
     *
     * @param string $value <p>
     * Decoded URI query.
     * </p>
     *
     * @return string Percent-encoded URI query.
     */
    public static function encodeQuery (string $value):string {

        return self::encode(
            $value,
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~!$&'()*+,;=:@/?"
        );

    }

    /**
     * ### Encodes a URI fragment
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\Percent::encode() To percent-encode disallowed bytes.
     *
     * @param string $value <p>
     * Decoded URI fragment.
     * </p>
     *
     * @return string Percent-encoded URI fragment.
     */
    public static function encodeFragment (string $value):string {

        return self::encode(
            $value,
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~!$&'()*+,;=:@/?"
        );

    }

    /**
     * ### Encodes URI user information
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\Percent::encode() To percent-encode disallowed bytes.
     *
     * @param string $value <p>
     * Decoded URI user information.
     * </p>
     *
     * @return string Percent-encoded URI user information.
     */
    public static function encodeUserInfo (string $value):string {

        return self::encode(
            $value,
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~!$&'()*+,;=:"
        );

    }

    /**
     * ### Decodes a percent-encoded URI component
     *
     * Decodes percent-encoded octets without interpreting component delimiters or applying form-style decoding.
     *
     * Plus signs are preserved.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Encoding::rawUrlDecode() To decode percent-encoded octets.
     *
     * @param string $value <p>
     * Percent-encoded URI component.
     * </p>
     *
     * @return string Decoded component.
     */
    public static function decode (string $value):string {

        return Runtime\Encoding::rawUrlDecode($value);

    }

    /**
     * ### Percent-encodes disallowed bytes
     *
     * Preserves only bytes explicitly listed in the allowed character set.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the input value.
     * @uses \FireHub\Runtime\Str\SB\Search::contains() To check if a character is in the allowed set.
     * @uses \FireHub\Runtime\Char\SB::ord() To get the byte value of a character.
     * @uses \FireHub\Runtime\Str\SB\Transform::format() To format a string with a placeholder.
     *
     * @param string $value <p>
     * Input value.
     * </p>
     * @param string $allowed <p>
     * Allowed ASCII characters.
     * </p>
     *
     * @return string Percent-encoded value.
     */
    private static function encode (string $value, string $allowed):string {

        $encoded = '';

        $length = Runtime\Str\SB\Inspection::length($value);

        for ($index = 0; $index < $length; $index++) {

            $character = $value[$index];

            if (Runtime\Str\SB\Search::contains($character, $allowed)) {

                $encoded .= $character;

                continue;

            }

            $encoded .= Runtime\Str\SB\Transform::format(
                '%%%02X',
                Runtime\Char\SB::ord($character)
            );

        }

        return $encoded;

    }

}