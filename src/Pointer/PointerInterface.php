<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer;

/**
 * The interface implemented by pointers.
 */
interface PointerInterface
{
    /**
     * Get the atoms.
     *
     * @return array<string> The atoms.
     */
    public function atoms();

    /**
     * Returns true if this pointer has atoms.
     *
     * @return boolean True if this pointer has atoms.
     */
    public function hasAtoms();

    /**
     * Get the number of atoms in the pointer.
     *
     * @return integer The number of atoms in the pointer.
     */
    public function size();

    /**
     * Append the atoms of the supplied pointer to the end of this pointer.
     *
     * @param PointerInterface $pointer The pointer to join.
     *
     * @return PointerInterface The resulting pointer.
     */
    public function join(PointerInterface $pointer);

    /**
     * Append a sequence of atoms to the end of this pointer.
     *
     * @param string     $atom            The first atom to append.
     * @param string,... $additionalAtoms Additional atoms to append.
     *
     * @return PointerInterface The resulting pointer.
     */
    public function joinAtoms($atom);

    /**
     * Append a sequence of atoms to the end of this pointer.
     *
     * @param array<string> $atoms The atoms to append.
     *
     * @return PointerInterface The resulting pointer.
     */
    public function joinAtomSequence(array $atoms);

    /**
     * Get the parent of this pointer.
     *
     * @return PointerInterface|null The parent pointer, or null if there is no parent.
     */
    public function parent();

    /**
     * Get a string representation of this pointer.
     *
     * @return string A string representation of this pointer.
     */
    public function string();

    /**
     * Get a string representation of this pointer.
     *
     * @return string A string representation of this pointer.
     */
    public function __toString();
}
