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
use FireHub\Foundation\Char;
use FireHub\Foundation\Str\Case\Casing;
use FireHub\Foundation\Str\Pattern;
use FireHub\Foundation\Char\Exception\InvalidLengthException;
use PHPUnit\Framework\Attributes\ {
    CoversClass, Group, Small, TestWith
};

/**
 * ### Test immutable character value object with a high-level developer API
 * @since 1.0.0
 */
#[Small]
#[Group('char')]
#[CoversClass(Char::class)]
final class CharTest extends FireHubTestCase {

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testInvalidAscii ():void {

        $this->expectException(InvalidLengthException::class);

        new Char('test');

    }

    /**
     * @since 1.0.0
     *
     * @param string|false $expected
     * @param int $codepoint
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Char\Exception\InvalidLengthException
     *
     * @return void
     */
    #[TestWith(['F', 70])]
    #[TestWith(['🔥', 128293])]
    public function testFromCodepoint (string|false $expected, int $codepoint):void {

        self::assertSame($expected, Char::fromCodepoint($codepoint)->value());

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testCase ():void {

        self::assertInstanceOf(Casing::class, new Char('F')->case());

    }

    /**
     * @since 1.0.0
     *
     * @return void
     */
    public function testPattern ():void {

        self::assertInstanceOf(Pattern::class, new Char('F')->pattern());

    }

    /**
     * @since 1.0.0
     *
     * @param non-negative-int|false $expected
     * @param string $character
     *
     * @throws \FireHub\Core\Exception\FireHubException
     * @throws \FireHub\Core\Type\Exception\ValueObjectException
     * @throws \FireHub\Foundation\Char\Exception\InvalidLengthException
     *
     * @return void
     */
    #[TestWith([70, 'F'])]
    #[TestWith([128293, '🔥'])]
    public function testCodepoint (int|false $expected, string $character):void {

        self::assertSame($expected, new Char($character)->codepoint());

    }

}