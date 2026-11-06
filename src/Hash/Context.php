<?php declare(strict_types = 1);

/**
 * This file is part of the FireHub Project ecosystem
 *
 * @author Danijel Galić <danijel.galic@outlook.com>
 * @copyright 2026-present The FireHub Project - All rights reserved
 * @license https://opensource.org/license/Apache-2-0 Apache License, Version 2.0
 *
 * @php-version >=8.0
 * @package Foundation
 */

namespace FireHub\Foundation\Hash;

use FireHub\Runtime;
use FireHub\Runtime\Hash\Algorithm;
use HashContext;

/**
 * ### Incremental Stream Hash Context
 *
 * Maintains an incremental hash of bytes successfully processed by stream decorators.
 *
 * The digest can be retrieved repeatedly without finalizing the underlying hashing context.
 * @since 1.0.0
 */
final class Context {

    /**
     * ### Native hashing context
     * @since 1.0.0
     */
    private HashContext $context;

    /**
     * ### Number of hashed bytes
     * @since 1.0.0
     *
     * @var non-negative-int
     */
    private int $bytes = 0;

    /**
     * ### Constructor
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Hash::initialize() To initialize the hashing context.
     *
     * @param \FireHub\Runtime\Hash\Algorithm $algorithm [optional] <p>
     * Hashing algorithm.
     * </p>
     *
     * @return void
     */
    public function __construct (
        private readonly Algorithm $algorithm = Algorithm::SHA256
    ) {

        $this->context = Runtime\Hash::initialize($algorithm);

    }

    /**
     * ### Updates the hash with additional bytes
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Hash::update() To update the hashing context.
     * @uses \FireHub\Runtime\Str\SB\Inspection::length() To get the length of the string.
     *
     * @param string $data <p>
     * Bytes to include in the hash.
     * </p>
     *
     * @return void
     */
    public function update (string $data):void {

        if ($data === '') return;

        Runtime\Hash::update(
            $this->context,
            $data
        );

        $this->bytes += Runtime\Str\SB\Inspection::length($data);

    }

    /**
     * ### Gets the current hash digest
     * @since 1.0.0
     *
     * @uses \FireHub\Runtime\Hash::digest() To get the hash digest.
     *
     * @param bool $binary [optional] <p>
     * Whether to return the raw binary digest.
     * </p>
     *
     * @return non-empty-string Hash digest.
     */
    public function digest (bool $binary = false):string {

        return Runtime\Hash::digest(
            $this->context,
            $binary
        );

    }

    /**
     * ### Gets the number of hashed bytes
     * @since 1.0.0
     *
     * @return non-negative-int Number of hashed bytes.
     */
    public function bytes ():int {

        return $this->bytes;

    }

    /**
     * ### Gets the hashing algorithm
     * @since 1.0.0
     *
     * @return \FireHub\Runtime\Hash\Algorithm Hashing algorithm.
     */
    public function algorithm ():Algorithm {

        return $this->algorithm;

    }

}