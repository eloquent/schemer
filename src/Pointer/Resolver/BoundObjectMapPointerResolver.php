<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Resolver;

use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Pointer\PointerInterface;

/**
 * Resolves pointers into a bound object map value.
 */
class BoundObjectMapPointerResolver implements BoundPointerResolverInterface
{
    /**
     * Construct a new bound object map pointer resolver.
     *
     * @param mixed &$value The value.
     */
    public function __construct(&$value)
    {
        $this->value = $value;
        $this->index = [];
    }

    /**
     * Get the value.
     *
     * @return mixed The value.
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Resolve a pointer within the value tree.
     *
     * @param PointerInterface $pointer The pointer.
     *
     * @return tuple<mixed,boolean> A 2-tuple containing the resolved value if successful, and a boolean indicating success.
     */
    public function resolve(PointerInterface $pointer)
    {
        if (!$pointer->hasAtoms()) {
            return [&$this->value, true];
        }

        $finalValue = &$this->value;
        $currentPointer = new Pointer;

        foreach ($pointer->atoms() as $atom) {
            $currentPointer = $currentPointer->joinAtoms($atom);
            $currentPointerString = $currentPointer->string();

            if (array_key_exists($currentPointerString, $this->index)) {
                $finalValue = &$this->index[$currentPointerString];

                continue;
            }

            if (is_object($finalValue) && property_exists($finalValue, $atom)) {
                $finalValue = &$finalValue->$atom;
            } elseif (
                is_array($finalValue) && (is_int($atom) || ctype_digit($atom))
            ) {
                $finalValue = &$finalValue[$atom];
            } else {
                return [null, false];
            }

            $this->index[$currentPointerString] = &$finalValue;
        }

        return [&$finalValue, true];
    }

    private $value;
    private $index;
}
