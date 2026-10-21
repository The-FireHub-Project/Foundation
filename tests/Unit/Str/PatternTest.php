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
use FireHub\Foundation\Str\Pattern;
use FireHub\Foundation\Str;
use FireHub\Foundation\Str\Pattern\ {
    Matcher, Replacer
};
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small
};

/**
 * ### Test access to pattern-based string operations
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Pattern::class)]
final class PatternTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testMatch ():void {

        self::assertInstanceOf(Matcher::class, new Pattern(new Str(''))->match());

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testReplace ():void {

        self::assertInstanceOf(Replacer::class, new Pattern(new Str(''))->replace(''));

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testRemove ():void {

        self::assertInstanceOf(Replacer::class, new Pattern(new Str(''))->remove());

    }

}