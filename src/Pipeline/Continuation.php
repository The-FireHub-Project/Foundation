<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.2
 * @package Foundation
 */

namespace FireHub\Foundation\Pipeline;

use FireHub\Core\Boundary\Pipeline\Next;
use Closure;

/**
 * ### Represents a pipeline execution continuation
 *
 * Encapsulates the next executable step within a processing pipeline.
 *
 * A continuation delegates the received input to its underlying handler and returns the resulting output.
 *
 * The handler may represent another processing stage or the terminal pipeline handler.
 *
 * Continuations are immutable and may be invoked multiple times. The execution behavior and state management of
 * the underlying handler remain its own responsibility.
 * @since 1.0.0
 *
 * @template TInput
 * @template TOutput
 *
 * @implements \FireHub\Core\Boundary\Pipeline\Next<TInput, TOutput>
 */
final readonly class Continuation implements Next {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param Closure(TInput):TOutput $handler <p>
     * Handler responsible for executing the next pipeline step.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private Closure $handler
    ) {}

    /**
     * ### Executes the continuation
     *
     * Delegates the provided input to the underlying handler and returns its result.
     * @since 1.0.0
     *
     * @param TInput $input <p>
     * Input passed to the next pipeline step.
     * </p>
     *
     * @return TOutput Result produced by the handler.
     */
    public function handle (mixed $input):mixed {

        return ($this->handler)($input);

    }

}