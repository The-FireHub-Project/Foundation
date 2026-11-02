<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\Uri\Component;

use FireHub\Core\Type\ValueObject;
use FireHub\Foundation\Uri\Encoding\Percent;
use FireHub\Foundation\Uri\Exception\InvalidPathException;
use FireHub\Runtime;

/**
 * ### Represents a URI path
 *
 * Preserves the original path and percent-encoded sequences.
 * @since 1.0.0
 *
 * @template TValue of string
 *
 * @extends \FireHub\Core\Type\ValueObject<TValue>
 */
final readonly class Path extends ValueObject {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To validate the path.
     *
     * @param TValue $path <p>
     * URI path.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct(
        private string $path
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                    '/\A(?:[A-Za-z0-9._~!$&\'()*+,;=:@\/-]|%[A-Fa-f0-9]{2})*\z/D',
                    $path
                ),
            fn() => new InvalidPathException
        );

    }

    /**
     * ### Creates a URI path from a decoded string
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\Percent::encodePath() To encode the path.
     *
     * @param string $path <p>
     * Decoded URI path.
     * </p>
     *
     * @throws \FireHub\Foundation\Uri\Exception\InvalidPathException If path has invalid pattern.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self<string> Encoded URI path.
     */
    public static function fromDecoded (string $path):self {

        return new self(Percent::encodePath($path));

    }

    /**
     * ### Gets the decoded URI path
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\PercentEncoder::decode() To decode the path.
     *
     * @return string Decoded URI path.
     */
    public function decoded ():string {

        return Percent::decode($this->path);

    }

    /**
     * ### Checks whether the path represents the root
     * @since 1.0.0
     *
     * @return bool True if the path is exactly "/", false otherwise.
     */
    public function isRoot ():bool {

        return $this->path === '/';

    }

    /**
     * ### Checks whether the path is absolute
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if the path starts with a slash.
     *
     * @return bool True if the path is absolute, false otherwise.
     */
    public function isAbsolute ():bool {

        return Runtime\Str\SB\Search::startsWith('/', $this->path);

    }

    /**
     * ### Checks whether the path is empty
     * @since 1.0.0
     *
     * @return bool True if the path is empty, false otherwise.
     */
    public function isEmpty ():bool {

        return $this->path === '';

    }

    /**
     * ### Gets the normalized path
     *
     * Normalizes percent-encoded sequences without changing the path segment structure.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\Uri\Encoding\Percent::normalize() To normalize the path.
     *
     * @return string Normalized path.
     */
    public function normalized ():string {

        return Percent::normalize($this->path);

    }

    /**
     * @inheritdoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->path;

    }

    /**
     * ### Removes dot segments
     *
     * Removes "." and ".." segments according to RFC 3986 Section 5.2.4.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Search::startsWith() To check if a segment starts with charaters.
     * @uses \FireHub\Runtime\Str\SB\Access::part() To get a part of a string.
     * @uses \FireHub\Runtime\Str\SB\Regex::replace() To replace a substring.
     * @uses \FireHub\Runtime\Str\SB\Search::firstPosition() To find the first occurrence of a character.
     *
     * @return self<string> Path without dot segments.
     */
    public function removeDotSegments ():self {

        $input = $this->path; $output = '';
        while ($input !== '') {

            if (Runtime\Str\SB\Search::startsWith('../', $input))
                $input = Runtime\Str\SB\Access::part($input, 3);

            else if (Runtime\Str\SB\Search::startsWith('./', $input))
                $input = Runtime\Str\SB\Access::part($input, 2);

            else if (Runtime\Str\SB\Search::startsWith('/./', $input))
                $input = Runtime\Str\SB\Access::part($input, 2);

            else if ($input === '/.')
                $input = '/';

            else if (Runtime\Str\SB\Search::startsWith('/../', $input)) {

                $input = Runtime\Str\SB\Access::part($input, 3);
                $output = Runtime\Str\SB\Regex::replace('~/[^/]*$~', '', $output);

            } else if ($input === '/..') {

                $input = '/';
                $output = Runtime\Str\SB\Regex::replace('~/[^/]*$~', '', $output);

            } else if ($input === '.' || $input === '..') {

                $input = '';

            } else {

                $position = $input[0] === '/'
                    ? Runtime\Str\SB\Search::firstPosition('/', $input, offset: 1)
                    : Runtime\Str\SB\Search::firstPosition('/', $input);

                if ($position === false) {

                    $output .= $input;
                    $input = '';

                } else {

                    $output .= Runtime\Str\SB\Access::part($input, 0, $position);
                    $input = Runtime\Str\SB\Access::part($input, $position);

                }

            }

        }

        return new self($output);

    }

}