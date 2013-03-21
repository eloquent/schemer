<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Resolver;

use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\ValueInterface;

class PointerResolver implements PointerResolverInterface
{
    /**
     * @param PointerInterface $pointer
     * @param ValueInterface   $value
     *
     * @return ValueInterface
     * @throws Exception\UndefinedValueException
     */
    public function resolve(PointerInterface $pointer, ValueInterface $value)
    {
        if (!$pointer->hasAtoms()) {
            return $value;
        }

        $atoms = $pointer->atoms();

        return $this->resolveAtoms($pointer, $atoms, $value);
    }

    /**
     * @param PointerInterface $pointer
     * @param array<string>    &$atoms
     * @param ValueInterface   $value
     *
     * @return ValueInterface
     * @throws Exception\UndefinedValueException
     */
    protected function resolveAtoms(
        PointerInterface $pointer,
        array &$atoms,
        ValueInterface $value
    ) {
        $atom = array_shift($atoms);

        if ($value instanceof ObjectValue) {
            $value = $this->resolveObject($pointer, $atom, $value);
        } elseif ($value instanceof ArrayValue) {
            $value = $this->resolveArray($pointer, $atom, $value);
        } else {
            throw new Exception\UndefinedValueException($pointer);
        }

        if (count($atoms) > 0) {
            return $this->resolveAtoms($pointer, $atoms, $value);
        }

        return $value;
    }

    /**
     * @param PointerInterface $pointer
     * @param string           $atom
     * @param ObjectValue      $value
     *
     * @return ValueInterface
     * @throws Exception\UndefinedValueException
     */
    protected function resolveObject(
        PointerInterface $pointer,
        $atom,
        ObjectValue $value
    ) {
        if ('' === $atom) {
            $atom = '_empty_';
        }

        $value = $value->value();
        if (!property_exists($value, $atom)) {
            throw new Exception\UndefinedValueException($pointer);
        }

        return $value->$atom;
    }

    /**
     * @param PointerInterface $pointer
     * @param string           $atom
     * @param ArrayValue       $value
     *
     * @return ValueInterface
     * @throws Exception\UndefinedValueException
     */
    protected function resolveArray(
        PointerInterface $pointer,
        $atom,
        ArrayValue $value
    ) {
        if (!ctype_digit($atom)) {
            throw new Exception\UndefinedValueException($pointer);
        }
        $atom = intval($atom);
        $value = $value->value();
        if (!array_key_exists($atom, $value)) {
            throw new Exception\UndefinedValueException($pointer);
        }

        return $value[$atom];
    }
}
