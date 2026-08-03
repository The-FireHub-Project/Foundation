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
use FireHub\Foundation\Str\Pattern\Matcher;
use FireHub\Foundation\Str;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Matches strings against regular expressions
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Matcher::class)]
final class MatcherTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param bool $expected
     * @param string $string
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith([true, 'the firehub project', 'firehub'])]
    public function testCustom (bool $expected, string $string, string $pattern):void {

        self::assertSame(
            $expected,
            new Matcher(new Str($string), 0)->custom($pattern)
        );

    }

}