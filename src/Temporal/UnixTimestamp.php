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

use FireHub\Core\Type\Temporal\Timestamp;
use FireHub\Core\Meta\Enum\{
    Date\Epoch, Side
};
use FireHub\Foundation\Temporal\Exception\InvalidTimestampException;
use FireHub\Runtime;

/**
 * ### Provides an immutable Unix timestamp value object with a high-level developer API
 *
 * The UnixTimestamp class represents an exact point in time as a signed number of seconds relative to the Unix epoch,
 * with optional fractional-second precision.
 *
 * The Unix epoch is defined as 1970-01-01T00:00:00+00:00 and serves as the canonical reference point for all values
 * represented by this class.
 *
 * It provides an expressive and object-oriented interface for creating, inspecting, comparing, converting, and
 * manipulating Unix timestamp values while preserving immutable value semantics inherited from the Core Timestamp
 * Value Object.
 *
 * Alternative temporal epochs can be specified when creating a UnixTimestamp through explicit epoch conversion. Their
 * values are normalized to the Unix epoch representation internally.
 *
 * The class is responsible for high-level Unix timestamp operations and developer experience, while low-level timestamp
 * calculations and execution remain delegated to the Runtime layer.
 * @since 1.0.0
 *
 * @template TValue of numeric-string
 *
 * @extends \FireHub\Core\Type\Temporal\Timestamp<TValue>
 *
 * @phpstan-consistent-constructor
 */
readonly class UnixTimestamp extends Timestamp {

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @param int $seconds <p>
     * The number of seconds since the epoch.
     * </p>
     * @param int<0,999999> $fraction [optional] <p>
     * The fractional part of the timestamp, in microseconds.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException If the timestamp is invalid.
     *
     * @return void
     */
    public function __construct (
        protected int $seconds,
        protected int $fraction = 0
    ) {

        $this->guard(
            static fn() => $fraction >= 0 && $fraction <= 999999,
            static fn() => new InvalidTimestampException(
                'Fractional part of the timestamp must be between 0 and 999999.',
                [
                    'min_fraction' => 0,
                    'max_fraction' => 999999,
                ]
            )
        );

    }

    /**
     * ### Creates a new UnixTimestamp instance from an epoch
     *
     * <code>
     * use FireHub\Foundation\Temporal\UnixTimestamp;
     * use FireHub\Core\Meta\Enum\Date\Epoch;
     *
     * $datetime = UnixTimestamp::fromEpoch(Epoch::GPS, 100, 245)->value();
     *
     * // '315964900.000245'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Core\Meta\Enum\Date\Epoch::unixOffset() To get the Unix epoch offset.
     *
     * @param non-empty-string|\FireHub\Core\Meta\Enum\Date\Epoch $epoch <p>
     * The epoch to use for the timestamp.
     * </p>
     * @param int $seconds <p>
     * The number of seconds relative to the specified epoch.
     * </p>
     * @param int<0,999999> $fraction [optional] <p>
     * The fractional part of the timestamp, in microseconds.
     * </p>
     *
     * @throws \FireHub\Core\Exception\FireHubException If the condition is not met.
     * @throws \FireHub\Core\Type\Exception\ValueObjectException If the exception is not a FireHubException.
     * @throws \FireHub\Foundation\Temporal\Exception\InvalidTimestampException If the timestamp is invalid.
     *
     * @return static<numeric-string> Returns a new UnixTimestamp instance.
     */
    public static function fromEpoch (string|Epoch $epoch, int $seconds, int $fraction = 0):static {

        if ($epoch instanceof Epoch) {

            $offset = $epoch->unixOffset();

        } else {

            $epoch = DateTime::from($epoch);

            $offset = $epoch->timestamp()->value();

        }

        return new static(
            $seconds + (int)$offset,
            $fraction
        );

    }

    /**
     * {@inheritDoc}
     *
     * <code>
     * use FireHub\Foundation\Temporal\UnixTimestamp;
     *
     * $datetime = new UnixTimestamp(100, 245)->value();
     *
     * // '100.000245'
     * </code>
     *
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Str\SB\Transform::pad() To pad the fractional part with zeros.
     *
     * @throws \FireHub\Runtime\Exception\EmptyPadException If the pad is empty.
     *
     * @return numeric-string The timestamp value as a string.
     */
    public function value ():string {

        /** @var numeric-string&non-falsy-string */
        return $this->seconds.'.'.Runtime\Str\SB\Transform::pad(
                (string)$this->fraction,
                6,
                '0',
                Side::LEFT
            );

    }

}