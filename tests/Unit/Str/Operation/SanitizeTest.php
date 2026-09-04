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
use FireHub\Foundation\Str\Operation\Sanitize;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test Sanitizes string content by removing or normalizing unsafe elements
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Sanitize::class)]
final class SanitizeTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @param string $expected
     * @param string $string
     * @param null|string|array<int, string> $allowed_tags
     *
     * @return void
     */
    #[TestWith([
        'Test paragraph. Other text',
        '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>'
    ])]
    #[TestWith([
        '<p>Test paragraph.</p> <a href="#fragment">Other text</a>',
        '<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>',
        ['p', 'a']
    ])]
    public function testStripTags (string $expected, string $string, null|string|array $allowed_tags = null):void {

        self::assertSame($expected, new Str($string)->sanitize()->stripTags($allowed_tags)->value());

    }

}