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
use FireHub\Foundation\Str\Pattern\Replacer;
use FireHub\Foundation\Str;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Replaces string content using regular expressions
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Replacer::class)]
final class ReplacerTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param string $replacement
     * @param string $pattern
     *
     * @throws \FireHub\Runtime\Exception\InvalidPatternException
     *
     * @return void
     */
    #[TestWith(['the firehub firehub', 'the firehub project', 'firehub', 'project'])]
    public function testCustom (string $expected, string $string, string $replacement, string $pattern):void {

        self::assertSame(
            $expected,
            new Replacer(new Str($string), $replacement, -1)->custom($pattern)->value()
        );

    }

}