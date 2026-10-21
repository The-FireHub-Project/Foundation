<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=7.4
 * @package Foundation\Tests
 */

namespace FireHub\Tests\Foundation\Unit;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Convert;
use FireHub\Foundation\Conversion\Policy\ {
    Safe, Strict
};
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test fluent API for value conversion
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Convert::class)]
final class ConvertTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param class-string $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([Safe::class, 'yes'])]
    public function testSafe (string $expected, mixed $value):void {

        self::assertInstanceOf($expected, new Convert($value)->safe());

    }

    /**
     * @since 1.0.0
     *
     * @param class-string $expected
     * @param mixed $value
     *
     * @return void
     */
    #[TestWith([Strict::class, 'yes'])]
    public function testStrict (string $expected, mixed $value):void {

        self::assertInstanceOf($expected, new Convert($value)->strict());

    }

}