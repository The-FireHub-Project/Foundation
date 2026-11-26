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

namespace FireHub\Foundation\IO\Storage;

use FireHub\Core\Type\StringValue;
use FireHub\Core\Boundary\IO\Storage\Path as PathContract;
use FireHub\Foundation\IO\Storage\Exception\InvalidPathException;
use FireHub\Runtime;

/**
 * ### Storage path
 *
 * Represents an immutable logical path relative to a storage disk.
 *
 * Storage paths use forward slashes as segment separators regardless of the underlying operating system or storage
 * technology.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\StringValue<non-empty-string>
 */
final readonly class Path extends StringValue implements PathContract {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Path::isValid() To validate the storage path.
     * @uses \FireHub\Core\Type\ValueObject::guard() As a guard.
     *
     * @param non-empty-string $value <p>
     * Storage-relative logical path.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {

        $this->guard(
            fn() => $this->isValid($value),
            fn() => new InvalidPathException
        );

    }

    /**
     * ### Gets the final path segment
     *
     * <code>
     * new Path('documents/report.pdf')->name();
     * // 'report.pdf'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Path::segments() To get the path segments.
     * @uses \FireHub\Runtime\Arr\Access::lastKey() To get the last key in the segments array.
     *
     * @return non-empty-string Final path segment.
     */
    public function name ():string {

        $segments = $this->segments();

        /** @var non-negative-int $key */
        $key = Runtime\Arr\Access::lastKey($segments);

        return $segments[$key]; // @phpstan-ignore offsetAccess.notFound

    }

    /**
     * ### Gets the parent storage path
     *
     * <code>
     * new Path('documents/report.pdf')->parent();
     * // Path('documents')
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\IO\Storage\Path::segments() To get the path segments.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of segments.
     * @uses \FireHub\Runtime\Arr\Mutation::pop() To remove the last segment from the segments array.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::implode() To join the segments back into a path.
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return null|self Parent path or null if no parent path exists.
     */
    public function parent ():?self {

        $segments = $this->segments();

        if (Runtime\Arr\Inspection::count($segments) === 1)
            return null;

        Runtime\Arr\Mutation::pop($segments);

        /** @var non-empty-string $value */
        $value = Runtime\Str\SB\Delimiter::implode($segments, '/');

        return new self($value);

    }

    /**
     * ### Gets the storage path depth
     *
     * <code>
     * new Path('documents/reports/annual.pdf')->depth();
     * // 3
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of path segments.
     *
     * @return positive-int Number of path segments.
     */
    public function depth ():int {

        /** @var positive-int */
        return Runtime\Arr\Inspection::count($this->segments());

    }

    /**
     * ### Joins another path to this storage path
     *
     * <code>
     * new Path('documents')->join('reports/annual.pdf');
     * // Path('documents/reports/annual.pdf')
     * </code>
     *
     * @since 1.0.0
     *
     * @param string|\FireHub\Core\Boundary\IO\Storage\Path $path <p>
     * Relative path to append.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidPathException If the storage path is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return self Combined storage path.
     */
    public function join (string|PathContract $path):self {

        $path = $path instanceof PathContract
            ? $path->value()
            : $path;

        return new self($this->value.'/'.$path);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * new Path('documents/reports/annual.pdf')->segments();
     * // ['documents', 'reports', 'annual.pdf']
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the path into segments.
     */
    public function segments ():array {

        /** @var non-empty-list<non-empty-string> */
        return Runtime\Str\SB\Delimiter::explode($this->value, '/');

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

    /**
     * ### Determines whether a storage path is valid
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To check for invalid characters.
     * @uses \FireHub\Runtime\Str\SB\Delimiter::explode() To split the path into segments.
     *
     * @param string $value <p>
     * Storage path to validate.
     * </p>
     *
     * @return bool True if the storage path is valid, false otherwise.
     */
    private function isValid (string $value):bool {

        if (Runtime\Str\SB\Regex::match('/[\x00-\x1F\x7F\\\\:]/', $value))
            return false;

        foreach (Runtime\Str\SB\Delimiter::explode($value, '/') as $segment)
            if ($segment === '' || $segment === '.' || $segment === '..')
                return false;

        return true;

    }

}