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

namespace FireHub\Foundation\IO\Storage;

use FireHub\Core\Type\StringValue;
use FireHub\Foundation\IO\Storage\Exception\InvalidDiskNameException;
use FireHub\Runtime;

/**
 * ### Storage disk name
 *
 * Represents the immutable identifier of a configured storage disk.
 *
 * Disk names are case-sensitive and use a restricted ASCII character set to provide consistent identifiers across
 * different environments.
 * @since 1.0.0
 *
 * @extends \FireHub\Core\Type\StringValue<non-empty-string>
 */
final readonly class Name extends StringValue {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Type\ValueObject::guard() To validate the disk name.
     * @uses \FireHub\Runtime\Str\SB\Regex::match() To match allowed characters.
     *
     * @param non-empty-string $value <p>
     * Storage disk identifier.
     * </p>
     *
     * @throws \FireHub\Foundation\IO\Storage\Exception\InvalidDiskNameException If the disk name is invalid.
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     *
     * @return void
     */
    public function __construct (
        private string $value
    ) {

        $this->guard(
            fn() => Runtime\Str\SB\Regex::match(
                '/\A[a-z][a-z0-9_-]*\z/',
                $value
            ),
            fn() => new InvalidDiskNameException
        );

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function value ():string {

        return $this->value;

    }

}