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
use FireHub\Foundation\Str;
use FireHub\Foundation\Str\Operation\Escape;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Escapes and unescapes string content safely
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Escape::class)]
final class EscapeTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param array<int, string> $characters $characters
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException
     *
     * @return void
     */
    #[TestWith(["O\'Reilly", "O'Reilly"])]
    public function testAddSlashes (string $expected, string $string, array $characters = ["'", '"', "\\"]):void {

        self::assertSame($expected, new Str($string)->escape()->addSlashes($characters)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param array<int, string> $characters $characters
     *
     * @throws \FireHub\Runtime\Exception\StringSplitLengthException
     *
     * @return void
     */
    #[TestWith(["O'Reilly", "O\'Reilly"])]
    public function testRemoveSlashes (string $expected, string $string, array $characters = ["'", '"', "\\"]):void {

        self::assertSame($expected, new Str($string)->escape()->removeSlashes($characters)->value());

    }

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     *
     * @return void
     */
    #[TestWith(['FireHub\.\*', 'FireHub.*'])]
    public function testQuoteMeta (string $expected, string $string):void {

        self::assertSame($expected, new Str($string)->escape()->quoteMeta()->value());

    }

}