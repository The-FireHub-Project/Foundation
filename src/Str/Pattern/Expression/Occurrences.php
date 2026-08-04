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

namespace FireHub\Foundation\Str\Pattern\Expression;

use FireHub\Foundation\Str\Pattern\Expression;
use FireHub\Foundation\Str\Exception\PatternOccurrencesNumberException;

/**
 * ### Controls the number of pattern occurrences
 *
 * Creates a regular expression that limits how many times a pattern can occur by defining minimum and maximum
 * occurrence boundaries.
 * @since 1.0.0
 */
final readonly class Occurrences implements Expression {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param null|non-negative-int $min [optional] <p>
     * The minimum number of occurrences required.
     * </p>
     * @param null|non-negative-int $max [optional] <p>
     * The maximum number of occurrences allowed.
     * </p>
     *
     * @throws \FireHub\Foundation\Str\Exception\PatternOccurrencesNumberException If both $min and $max are null, or
     * if $min is greater than $max, or if either $min or $max is negative.
     *
     * @return void
     */
    public function __construct (
        private ?int $min = null,
        private ?int $max = null
    ) {

        if ($min === null && $max === null) {
            throw new PatternOccurrencesNumberException(
                'At least one occurrence boundary must be defined.'
            );
        }

        if ($min !== null && $min < 0) {
            throw new PatternOccurrencesNumberException(
                'Minimum occurrences cannot be negative.'
            );
        }

        if ($max !== null && $max < 0) {
            throw new PatternOccurrencesNumberException(
                'Maximum occurrences cannot be negative.'
            );
        }

        if ($min !== null && $max !== null && $min > $max) {
            throw new PatternOccurrencesNumberException(
                'Minimum occurrences cannot exceed maximum occurrences.'
            );
        }

    }

    /**
     * @inheritDoc
     *
     * @since 1.0.0
     */
    public function regex (string $pattern):string {

        if ($this->min !== null && $this->max !== null) {
            return '^(?=(?:.*?'.$pattern.'){'.$this->min.','.$this->max.'}).*$';
        }

        if ($this->min !== null) {
            return '^(?=(?:.*?'.$pattern.'){'.$this->min.',}).*$';
        }

        return '^(?!(?:.*?'.$pattern.'){'.(($this->max ?? 0) + 1).',}).*$';

    }

}