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

namespace FireHub\Foundation\Temporal;

use FireHub\Core\Meta\Enum\Date\Format;
use FireHub\Core\Type\Temporal\Date as BaseDate;
use FireHub\Foundation\Temporal\Exception\InvalidDateException;
use DateTimeImmutable;

/**
 * ### Provides an immutable date value object with a high-level developer API
 *
 * The Date class represents a calendar date within the FireHub Foundation layer.
 *
 * It provides an expressive and object-oriented interface for working with calendar dates while preserving immutable
 * value semantics inherited from the Core Value Object system.
 *
 * The class is responsible for high-level date operations and developer experience, while low-level date and time
 * functionality remains delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of non-empty-string
 *
 * @extends \FireHub\Core\Type\Temporal\Date<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class Date extends BaseDate {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param DateTimeImmutable $value <p>
     * The date value in the normalized `Y-m-d` format.
     * </p>
     *
     * @return void
     */
    protected function __construct (
        protected DateTimeImmutable $value
    ) {}

    /**
     * ### Creates a date value from a formatted string
     *
     * Parses the given date value using the specified format and creates a new immutable Date value object.
     *
     * <code>
     * use FireHub\Foundation\Temporal\Date;
     *
     * $date = Date::from('2000-01-01');
     *
     * // '2000-01-01'
     *
     * $date = Date::from('01.01.2000', 'd.m.Y');
     *
     * // '01.01.2000'
     * </code>
     *
     * @since 1.0.0
     *
     * @param non-empty-string $value <p>
     * The date value to parse.
     * </p>
     *
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Format $format [optional] <p>
     * The format used to parse the date value.
     * </p>
     *
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidDateException If the date value cannot be parsed using
     * the given format.
     *
     * @return static<non-empty-string> A new Date instance.
     */
    public static function from (string $value, string|Format $format = Format::ISO_DATE):static {

        $format = $format instanceof Format ? $format->value : $format;

        $date = DateTimeImmutable::createFromFormat('!'.$format, $value);

        $errors = DateTimeImmutable::getLastErrors();

        if (
            $date === false
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
        ) throw new InvalidDateException("Date value could not be parsed using the $format format.");

        return new static($date);

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\Date;
     *
     * $date = Date::from('2000-01-01')->value();
     *
     * // '2000-01-01'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Meta\Enum\Date\Format::ISO_DATE As the temporal format.
     *
     * @return non-empty-string Raw VO value.
     */
    public function value ():string {

        return $this->value->format(Format::ISO_DATE->value);

    }

}