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

namespace FireHub\Tests\Foundation\Unit\Str;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Str\Ascii;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test an ASCII encoded string value object
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Ascii::class)]
final class AsciiTest extends FireHubTestCase {

}