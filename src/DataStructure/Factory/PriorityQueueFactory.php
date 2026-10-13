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

namespace FireHub\Foundation\DataStructure\Factory;

use FireHub\Foundation\DataStructure\PriorityQueue;
use Closure;

/**
 * ### Provides factory methods for creating priority queue data structures
 *
 * Provides convenient factory methods for creating minimum and maximum priority queues using the Foundation data
 * structure implementation.
 *
 * The factory encapsulates the priority comparison strategy required to construct a priority queue, allowing
 * callers to create priority queues with the desired ordering without directly configuring the underlying
 * binary heap storage.
 *
 * Priority queues created by this factory maintain stable ordering for entries with equivalent priorities.
 * @since 1.0.0
 */
readonly class PriorityQueueFactory {

    /**
     * ### Creates a minimum priority queue
     *
     * Creates a new empty priority queue configured to process entries with the smallest priorities first.
     *
     * The provided comparator determines the natural ordering of priorities. Entries with equivalent priorities
     * are processed in insertion order.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\PriorityQueue To create the priority queue.
     *
     * @template TPriority
     *
     * @param Closure(TPriority, TPriority):int $comparator <p>
     * Comparison strategy used to determine priority ordering.
     *
     * A negative result indicates that the first priority is smaller, a positive result indicates that the
     * second priority is smaller, and zero indicates equivalent priorities.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\PriorityQueue<TPriority, mixed> An empty minimum priority queue.
     */
    public function min (Closure $comparator):PriorityQueue {

        return new PriorityQueue($comparator);

    }

    /**
     * ### Creates a maximum priority queue
     *
     * Creates a new empty priority queue configured to process entries with the largest priorities first.
     *
     * The provided comparator determines the natural ordering of priorities. The comparison direction is
     * reversed so that larger priorities have precedence over smaller priorities.
     *
     * Entries with equivalent priorities are processed in insertion order.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\PriorityQueue To create the priority queue.
     *
     * @template TPriority
     *
     * @param Closure(TPriority, TPriority):int $comparator <p>
     * Comparison strategy used to determine priority ordering.
     *
     * A negative result indicates that the first priority is smaller, a positive result indicates that the
     * second priority is smaller, and zero indicates equivalent priorities.
     * </p>
     *
     * @return \FireHub\Foundation\DataStructure\PriorityQueue<TPriority, mixed> An empty maximum priority queue.
     */
    public function max (Closure $comparator):PriorityQueue {

        /** @var \FireHub\Foundation\DataStructure\PriorityQueue<TPriority, mixed> */
        return new PriorityQueue(
            static fn (mixed $left, mixed $right):int => $comparator($right, $left)
        );

    }

}