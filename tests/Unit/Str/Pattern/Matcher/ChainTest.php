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
use FireHub\Foundation\Str\Pattern\Matcher\Chain;
use FireHub\Foundation\Str\Pattern\Matcher;
use FireHub\Foundation\Str;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test fluent chain for combining multiple string-matching conditions
 * @since 1.0.0
 */
#[Small]
#[Group('str')]
#[CoversClass(Chain::class)]
final class ChainTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testCustom ():void {

        self::assertTrue(
            new Chain(new Matcher(new Str('the firehub project'), 0))
                ->startsWith('the')
                ->contains('firehub')
                ->execute()
        );

    }

}