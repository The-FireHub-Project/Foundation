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

namespace FireHub\Foundation\DataStructure\Storage;

use FireHub\Core\Boundary\Capability\ {
    Access\FrontAccess,
    Measurement\Metrics,
    Mutation\FrontRemoval, Mutation\Insertion,
    Cloneable, Forkable
};
use FireHub\Core\Type\Maybe;
use FireHub\Foundation\DataStructure\Storage;
use FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit;
use FireHub\Foundation\Maybe\ {
    None, Some
};
use FireHub\Foundation\State\ {
    HasCopyOnWriteState, SharedState
};
use FireHub\Runtime;
use Closure;

/**
 * ### Provides array-backed binary heap storage
 *
 * Binary heap storage maintains a complete binary tree using a contiguous, zero-based array representation.
 * Values are organized according to a configurable comparison strategy that determines their relative
 * precedence within the heap.
 *
 * The storage maintains the heap invariant by ensuring that each parent has precedence over its children.
 * This invariant guarantees constant-time access to the root value and logarithmic-time insertion and removal,
 * without requiring the entire collection to be sorted.
 *
 * The comparison strategy determines whether the storage behaves as a minimum heap, maximum heap, or another
 * priority-based heap. A comparison result less than zero indicates that the first value has precedence over
 * the second value, while a result greater than zero indicates the opposite. Equal values have no defined
 * relative precedence.
 *
 * Values are stored directly in an array rather than individual node objects. Parent and child relationships
 * are calculated from array indexes, avoiding node allocation and pointer management overhead.
 *
 * Iteration exposes values in their internal heap order, which is not necessarily sorted. The first value
 * is always the current root of the heap.
 *
 * The storage provides the underlying heap organization for higher-level data structures such as heaps
 * and priority queues without prescribing their public APIs or application-specific priority semantics.
 * @since 1.0.0
 *
 * @template TValue
 *
 * @implements \FireHub\Foundation\DataStructure\Storage<int, TValue>
 * @implements \FireHub\Core\Boundary\Capability\Access\FrontAccess<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\FrontRemoval<TValue>
 * @implements \FireHub\Core\Boundary\Capability\Mutation\Insertion<TValue>
 *
 * @phpstan-type State list<TValue>
 * @phpstan-type Comparator Closure(TValue, TValue):int
 */
final class BinaryHeapStorage implements Storage, Cloneable, Forkable, Metrics, FrontAccess, Insertion,
    FrontRemoval {

    /**
     * ### Copy-on-write state
     * @since 1.0.0
     *
     * @use \FireHub\Foundation\State\HasCopyOnWriteState<State>
     */
    use HasCopyOnWriteState;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param Comparator $comparator <p>
     * Comparison strategy used to determine precedence between values.
     *
     * A negative result indicates that the first value has precedence, a positive result indicates that
     * the second value has precedence, and zero indicates equivalent precedence.
     * </p>
     * @param \FireHub\Foundation\DataStructure\Storage\Initializer<int, TValue> $initializer <p>
     * Initializes the storage with the provided values.
     * </p>
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initializer::initialize() To initialize the storage.
     * @uses \FireHub\Foundation\DataStructure\Storage::heapify() To build the binary heap.
     * @uses \FireHub\Runtime\Iterator::toArray() To convert the iterable to an array.
     * @uses \FireHub\Runtime\Arr\Access::values() To convert the iterable to an array.
     *
     * @return void
     */
    public function __construct (
        private readonly Closure $comparator,
        Initializer $initializer
    ) {

        $this->state = new SharedState(
            Runtime\Arr\Access::values(
                Runtime\Iterator::toArray($initializer->initialize())
            )
        );

        $this->heapify();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\Initialization\EmptyInit To initialize the storage with an empty
     * array.
     */
    public function emptyCopy ():self {

        /** @var self<TValue> */
        return new self($this->comparator, new EmptyInit);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Copy::deep() To create an independent copy of stored values.
     *
     * @throws \FireHub\Runtime\Exception\CopyObjectException If copying a stored value fails.
     */
    protected function copyData (mixed $data):array {

        /** @var State */
        return Runtime\Copy::deep($data);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function iterate ():iterable {

        return $this->state->data();

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::size() To get the size of the storage.
     */
    public function isEmpty ():bool {

        return $this->size() === 0;

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the size of the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function size ():int {

        return Runtime\Arr\Inspection::count($this->state->data());

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\Maybe\Some As return value.
     * @uses \FireHub\Foundation\Maybe\None If the storage is empty.
     * @uses \FireHub\Runtime\Arr\Access::first() To get the first value.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     */
    public function first ():Maybe {

        if ($this->isEmpty()) return new None;

        /** @var TValue $first */
        $first = Runtime\Arr\Access::first($this->state->data());

        return new Some($first);

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage\BinaryHeapStorage::siftUp() To restore heap ordering toward the
     * root.
     * @uses \FireHub\Foundation\DataStructure\Storage::detach() To detach the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of values.
     */
    public function insert (mixed ...$values):void {

        if ($values === []) return;

        $this->detach();

        $data = &$this->state->data();

        foreach ($values as $value) {

            $index = Runtime\Arr\Inspection::count($data);

            $data[] = $value;

            $this->siftUp($index);

        }

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::isEmpty() To check if the storage is empty.
     * @uses \FireHub\Foundation\DataStructure\Storage::detach() To detach the storage.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Arr\Mutation::pop() To remove the first value from the storage.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of values.
     */
    public function removeFront ():Maybe {

        if ($this->isEmpty()) return new None;

        $this->detach();

        $data = &$this->state->data();

        $first = $data[0]; // @phpstan-ignore offsetAccess.notFound

        if (Runtime\Arr\Inspection::count($data) === 1) {

            Runtime\Arr\Mutation::pop($data);

            return new Some($first);

        }

        /** @var TValue $last */
        $last = Runtime\Arr\Mutation::pop($data);

        $data[0] = $last;

        $this->siftDown(0);

        return new Some($first);

    }

    /**
     * ### Restores heap ordering toward the root
     *
     * Moves the value at the specified index upward while it has precedence over its parent.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     * @uses \FireHub\Runtime\Math::divideInt() To calculate the index of the parent node.
     *
     * @param non-negative-int $index <p>
     * Index of the value to reposition.
     * </p>
     *
     * @return void
     */
    private function siftUp (int $index):void {

        $data = &$this->state->data();

        $value = $data[$index]; // @phpstan-ignore offsetAccess.notFound

        while ($index > 0) {

            $parent = Runtime\Math::divideInt($index - 1, 2);

            if (($this->comparator)($value, $data[$parent]) >= 0) // @phpstan-ignore offsetAccess.notFound
                break;

            $data[$index] = $data[$parent]; // @phpstan-ignore offsetAccess.notFound

            $index = $parent;

        }

        $data[$index] = $value;

    }

    /**
     * ### Restores heap ordering toward the leaves
     *
     * Moves the value at the specified index downward until it has precedence over its children.
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of values.
     * @uses \FireHub\Foundation\State\SharedState::data() To get the data of the storage.
     *
     * @param non-negative-int $index <p>
     * Index of the value to reposition.
     * </p>
     *
     * @return void
     */
    private function siftDown (int $index):void {

        $data = &$this->state->data();

        $size = Runtime\Arr\Inspection::count($data);

        $value = $data[$index]; // @phpstan-ignore offsetAccess.notFound

        while (true) {

            $left = ($index * 2) + 1;

            if ($left >= $size) break;

            $right = $left + 1;

            $child = $left;

            if (
                $right < $size
                && ($this->comparator)(
                    $data[$right], // @phpstan-ignore offsetAccess.notFound
                    $data[$left] // @phpstan-ignore offsetAccess.notFound
                ) < 0
            )
                $child = $right;

            if (($this->comparator)($value, $data[$child]) <= 0) // @phpstan-ignore offsetAccess.notFound
                break;

            $data[$index] = $data[$child]; // @phpstan-ignore offsetAccess.notFound

            $index = $child;

        }

        $data[$index] = $value;

    }

    /**
     * ### Builds the binary heap
     *
     * Organizes the current values into a valid binary heap using bottom-up heap construction.
     *
     * The operation processes all non-leaf nodes in reverse level order and restores the heap invariant
     * for each corresponding subtree.
     *
     * Construction takes linear time relative to the number of values.
     * @since 1.0.0
     *
     * @uses \FireHub\Foundation\DataStructure\Storage::siftDown() To restore heap ordering toward the leaves.
     * @uses \FireHub\Runtime\Arr\Inspection::count() To get the number of values.
     * @uses \FireHub\Runtime\Math::divideInt() To calculate the index of the parent node.
     *
     * @return void
     */
    private function heapify ():void {

        $size = Runtime\Arr\Inspection::count($this->state->data());

        if ($size < 2) return;

        for ($index = Runtime\Math::divideInt($size, 2) - 1; $index >= 0; $index--)
            $this->siftDown($index);

    }

}