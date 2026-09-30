<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.5
 * @package Foundation
 */

namespace FireHub\Foundation\DataStructure;

use FireHub\Core\Boundary\Runtime\NativeRuntime;
use FireHub\Foundation\DataStructure\Factory\ {
    BagFactory, DequeFactory, MapFactory, QueueFactory, SetFactory, StackFactory, StreamFactory, StructFactory,
    TupleFactory, VectorFactory
};

/**
 * ### Data Structure Factory
 *
 * Provides a fluent entry point for creating FireHub data structures.
 * @since 1.0.0
 */
readonly class DS extends NativeRuntime {

    /**
     * ### Vector factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\VectorFactory The Vector factory.
     */
    public static function vector ():VectorFactory {

        return new VectorFactory;

    }

    /**
     * ### Deque factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\DequeFactory The Deque factory.
     */
    public static function deque ():DequeFactory {

        return new DequeFactory;

    }

    /**
     * ### Stack factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\StackFactory The Stack factory.
     */
    public static function stack ():StackFactory {

        return new StackFactory;

    }

    /**
     * ### Queue factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\QueueFactory The Queue factory.
     */
    public static function queue ():QueueFactory {

        return new QueueFactory;

    }

    /**
     * ### Map factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\MapFactory The Map factory.
     */
    public static function map ():MapFactory {

        return new MapFactory;

    }

    /**
     * ### Set factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\SetFactory The Set factory.
     */
    public static function set ():SetFactory {

        return new SetFactory;

    }

    /**
     * ### Bag factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\BagFactory The Bag factory.
     */
    public static function bag ():BagFactory {

        return new BagFactory;

    }

    /**
     * ### Tuple factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\TupleFactory The Tuple factory.
     */
    public static function tuple ():TupleFactory {

        return new TupleFactory;

    }

    /**
     * ### Struct factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\StructFactory The Struct factory.
     */
    public static function struct ():StructFactory {

        return new StructFactory;

    }

    /**
     * ### Stream factory
     * @since 1.0.0
     *
     * @return \FireHub\Foundation\DataStructure\Factory\StreamFactory Stream factory.
     */
    public static function stream ():StreamFactory {

        return new StreamFactory();

    }

}