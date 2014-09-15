<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Factory;

use Eloquent\Schemer\Pointer\Factory\Exception\InvalidPointerException;
use Eloquent\Schemer\Pointer\PointerInterface;

/**
 * The interface implemented by pointer factories.
 */
interface PointerFactoryInterface
{
    /**
     * Create a new pointer from its string representation.
     *
     * @param string|null $pointer The pointer string.
     *
     * @return PointerInterface        The newly created pointer.
     * @throws InvalidPointerException If the supplied pointer string is invalid.
     */
    public function create($pointer);

    /**
     * Create a new pointer from the fragment portion of the supplied URI.
     *
     * @param string $uri The URI.
     *
     * @return PointerInterface        The newly created pointer.
     * @throws InvalidPointerException If the supplied pointer string is invalid.
     */
    public function createFromUri($uri);

    /**
     * Create a new pointer from a sequence of atoms.
     *
     * @param array<string> $atoms The atoms.
     *
     * @return PointerInterface The newly created pointer.
     */
    public function createFromAtoms(array $atoms);
}
