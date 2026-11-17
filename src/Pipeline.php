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

namespace FireHub\Foundation;

use FireHub\Core\Boundary\ {
    Pipeline as PipelineBoundary, Pipeline\Stage
};
use FireHub\Foundation\Pipeline\Continuation;
use FireHub\Runtime;
use Closure;

/**
 * ### Executes an immutable processing pipeline
 *
 * Composes an ordered sequence of processing stages around a terminal handler.
 *
 * Each stage receives an input value and a continuation representing the remaining execution chain.
 *
 * Stages may execute logic before or after delegation, transform the input passed downstream, modify the returned
 * result, or terminate processing without invoking the continuation.
 *
 * The execution chain is composed once during construction and reused for later executions.
 *
 * Pipeline immutability does not guarantee that individual stages or their dependencies are stateless.
 * @since 1.0.0
 *
 * @template TInput
 * @template TOutput
 *
 * @implements \FireHub\Core\Boundary\Pipeline<TInput, TOutput>
 */
final readonly class Pipeline implements PipelineBoundary {

    /**
     * ### Composed pipeline handler
     * @since 1.0.0
     *
     * @var Closure(TInput):TOutput
     */
    private Closure $handler;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Boundary\Pipeline\Stage::process() To execute a processing stage.
     * @uses \FireHub\Runtime\Arr\Transform::reverse() To reverse the provided stages.
     *
     * @param Closure(TInput):TOutput $handler <p>
     * Terminal handler executed after all stages delegate.
     * </p>
     * @param \FireHub\Core\Boundary\Pipeline\Stage<TInput, TOutput> ...$stages <p>
     * Processing stages in execution order.
     * </p>
     *
     * @return void
     */
    public function __construct (Closure $handler, Stage ...$stages) {

        foreach (Runtime\Arr\Transform::reverse($stages) as $stage) {

            $next = new Continuation($handler);

            $handler = static fn(mixed $input):mixed => $stage->process(
                $input,
                $next
            );

        }

        $this->handler = $handler;

    }

    /**
     * ### Executes the pipeline
     *
     * Passes the provided input to the precomposed execution chain.
     *
     * Processing stages execute in registration order, provided each preceding stage delegates to its continuation.
     *
     * If no processing stages were registered, the terminal handler executes directly.
     * @since 1.0.0
     *
     * @param TInput $input <p>
     * Input passed to the first processing stage.
     * </p>
     *
     * @return TOutput Result produced by the pipeline.
     */
    public function handle (mixed $input):mixed {

        return ($this->handler)($input);

    }

}