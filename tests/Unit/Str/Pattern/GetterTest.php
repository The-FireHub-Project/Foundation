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

namespace FireHub\Tests\Foundation\Unit\Str\Pattern;

use FireHub\Testing\FireHubTestCase;
use FireHub\Foundation\Str\Pattern\Getter;
use FireHub\Foundation\Str;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Retrieves all pattern matches from strings
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Getter::class)]
final class GetterTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param array<int, string> $expected
     * @param string $string
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([['firehub'], 'the firehub project', 'firehub'])]
    public function testCustom (array $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Getter(new Str($string), 0)->custom($pattern)
        );

    }

}