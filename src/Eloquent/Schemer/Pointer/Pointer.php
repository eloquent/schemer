<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer;

class Pointer implements PointerInterface
{
    /**
     * @param array<string>|null $atoms
     */
    public function __construct(array $atoms = null)
    {
        if (null === $atoms) {
            $atoms = array();
        }

        $this->atoms = $atoms;
    }

    /**
     * @return array<string>
     */
    public function atoms()
    {
        return $this->atoms;
    }

    /**
     * @return string|null
     */
    public function lastAtom()
    {
        if (!$this->hasAtoms()) {
            return null;
        }

        $atoms = $this->atoms();

        return array_pop($atoms);
    }

    /**
     * @return boolean
     */
    public function hasAtoms()
    {
        return count($this->atoms) > 0;
    }

    /**
     * @param PointerInterface $pointer
     *
     * @return PointerInterface
     */
    public function join(PointerInterface $pointer)
    {
        return new static(array_merge($this->atoms(), $pointer->atoms()));
    }

    /**
     * @param string     $atom
     * @param string,... $additionalAtoms
     *
     * @return PointerInterface
     */
    public function joinAtoms($atom)
    {
        return $this->joinAtomSequence(func_get_args());
    }

    /**
     * @param mixed<string> $atoms
     *
     * @return PointerInterface
     */
    public function joinAtomSequence($atoms)
    {
        $newAtoms = $this->atoms();
        foreach ($atoms as $atom) {
            array_push($newAtoms, $atom);
        }

        return new static($newAtoms);
    }

    /**
     * @return PointerInterface
     */
    public function parent()
    {
        if (!$this->hasAtoms()) {
            throw new Exception\NoParentException($this);
        }
        $atoms = $this->atoms();
        array_pop($atoms);

        return new static($atoms);
    }

    /**
     * @return string
     */
    public function string()
    {
        if (!$this->hasAtoms()) {
            return '';
        }

        return sprintf(
            '/%s',
            implode(
                '/',
                array_map(
                    function($value) {
                        return strtr($value, array('~' => '~0', '/' => '~1'));
                    },
                    $this->atoms()
                )
            )
        );
    }

    private $atoms;
}
