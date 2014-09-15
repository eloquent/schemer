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
use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Pointer\PointerInterface;

/**
 * Creates pointers from various inputs.
 */
class PointerFactory implements PointerFactoryInterface
{
    /**
     * Get a static pointer factory instance.
     *
     * @return PointerFactoryInterface The static pointer factory instance.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Create a new pointer from its string representation.
     *
     * @param string|null $pointer The pointer string.
     *
     * @return PointerInterface        The newly created pointer.
     * @throws InvalidPointerException If the supplied pointer string is invalid.
     */
    public function create($pointer)
    {
        if (null === $pointer) {
            return null;
        }

        $atoms = explode('/', $pointer);

        if ('' !== $atoms[0]) {
            throw new InvalidPointerException($pointer);
        }

        array_shift($atoms);

        foreach ($atoms as $index => $atom) {
            $atoms[$index] = strtr($atom, ['~0' => '~', '~1' => '/']);
        }

        return $this->createFromAtoms($atoms);
    }

    /**
     * Create a new pointer from the fragment portion of the supplied URI.
     *
     * @param string $uri The URI.
     *
     * @return PointerInterface        The newly created pointer.
     * @throws InvalidPointerException If the supplied pointer string is invalid.
     */
    public function createFromUri($uri)
    {
        $fragment = rawurldecode(parse_url($uri, PHP_URL_FRAGMENT));

        if (null === $fragment || '/' !== substr($fragment, 0, 1)) {
            return new Pointer;
        }

        return $this->create($fragment);
    }

    /**
     * Create a new pointer from a sequence of atoms.
     *
     * @param array<string> $atoms The atoms.
     *
     * @return PointerInterface The newly created pointer.
     */
    public function createFromAtoms(array $atoms)
    {
        return new Pointer($atoms);
    }

    private static $instance;
}
