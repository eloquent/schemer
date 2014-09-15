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

use Eloquent\Schemer\Pointer\PointerInterface;

/**
 * Resolves pointers into object map values.
 */
class ObjectMapPointerResolver implements PointerResolverInterface
{
    /**
     * Get a static object map pointer resolver instance.
     *
     * @return PointerResolverInterface The static object map pointer resolver instance.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Resolve a pointer within a value tree.
     *
     * @param mixed            &$value  The value.
     * @param PointerInterface $pointer The pointer.
     *
     * @return tuple<mixed,boolean> A 2-tuple containing the resolved value if successful, and a boolean indicating success.
     */
    public function resolve(&$value, PointerInterface $pointer)
    {
        if (!$pointer->hasAtoms()) {
            return [&$value, true];
        }

        $finalValue = &$value;

        foreach ($pointer->atoms() as $atom) {
            if (is_object($finalValue) && property_exists($finalValue, $atom)) {
                $finalValue = &$finalValue->$atom;
            } elseif (
                is_array($finalValue) && (is_int($atom) || ctype_digit($atom))
            ) {
                $finalValue = &$finalValue[$atom];
            } else {
                return [null, false];
            }
        }

        return [&$finalValue, true];
    }

    private static $instance;
}
