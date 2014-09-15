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

use Eloquent\Schemer\Pointer\Factory\Exception\InvalidPointerException;
use Eloquent\Schemer\Pointer\Factory\PointerFactory;

/**
 * Represents a pointer into an value tree.
 */
class Pointer implements PointerInterface
{
    /**
     * Create a new pointer from its string representation.
     *
     * @param string|null $pointer The pointer string.
     *
     * @return PointerInterface        The newly created pointer.
     * @throws InvalidPointerException If the supplied pointer string is invalid.
     */
    public static function create($pointer)
    {
        return static::factory()->create($pointer);
    }

    /**
     * Create a new pointer from the fragment portion of the supplied URI.
     *
     * @param string $uri The URI.
     *
     * @return PointerInterface        The newly created pointer.
     * @throws InvalidPointerException If the supplied pointer string is invalid.
     */
    public static function fromUri($uri)
    {
        return static::factory()->createFromUri($uri);
    }

    /**
     * Construct a new pointer.
     *
     * @param array<string> $atoms The atoms.
     */
    public function __construct(array $atoms = null)
    {
        if (null === $atoms) {
            $atoms = [];
        }

        $this->atoms = $atoms;
        $this->size = count($atoms);
    }

    /**
     * Get the atoms.
     *
     * @return array<string> The atoms.
     */
    public function atoms()
    {
        return $this->atoms;
    }

    /**
     * Returns true if this pointer has atoms.
     *
     * @return boolean True if this pointer has atoms.
     */
    public function hasAtoms()
    {
        return $this->size > 0;
    }

    /**
     * Get the number of atoms in the pointer.
     *
     * @return integer The number of atoms in the pointer.
     */
    public function size()
    {
        return $this->size;
    }

    /**
     * Append the atoms of the supplied pointer to the end of this pointer.
     *
     * @param PointerInterface $pointer The pointer to join.
     *
     * @return PointerInterface The resulting pointer.
     */
    public function join(PointerInterface $pointer)
    {
        if (!$pointer->hasAtoms()) {
            return $this;
        }

        return new static(array_merge($this->atoms, $pointer->atoms()));
    }

    /**
     * Append a sequence of atoms to the end of this pointer.
     *
     * @param string     $atom            The first atom to append.
     * @param string,... $additionalAtoms Additional atoms to append.
     *
     * @return PointerInterface The resulting pointer.
     */
    public function joinAtoms($atom)
    {
        return new static(array_merge($this->atoms, func_get_args()));
    }

    /**
     * Append a sequence of atoms to the end of this pointer.
     *
     * @param array<string> $atoms The atoms to append.
     *
     * @return PointerInterface The resulting pointer.
     */
    public function joinAtomSequence(array $atoms)
    {
        if (!$atoms) {
            return $this;
        }

        return new static(array_merge($this->atoms, $atoms));
    }

    /**
     * Get the parent of this pointer.
     *
     * @return PointerInterface The parent pointer.
     */
    public function parent()
    {
        if ($this->size < 1) {
            return null;
        }

        $atoms = $this->atoms;
        array_pop($atoms);

        return new static($atoms);
    }

    /**
     * Get a string representation of this pointer.
     *
     * @return string A string representation of this pointer.
     */
    public function string()
    {
        if ($this->size < 1) {
            return '';
        }

        return sprintf(
            '/%s',
            implode(
                '/',
                array_map(
                    function ($value) {
                        return strtr($value, ['~' => '~0', '/' => '~1']);
                    },
                    $this->atoms
                )
            )
        );
    }

    /**
     * Get a string representation of this pointer.
     *
     * @return string A string representation of this pointer.
     */
    public function __toString()
    {
        return $this->string();
    }

    /**
     * Get a static instance of an appropriate factory for this type of pointer.
     *
     * @return PointerFactoryInterface The pointer factory.
     */
    protected static function factory()
    {
        return PointerFactory::instance();
    }

    private $atoms;
    private $size;
}
