<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference;

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Pointer\PointerInterface;
use Zend\Uri\UriInterface;

class ResolutionScopeMap
{
    /**
     * @param array<tuple<PointerInterface,UriInterface>> $map
     * @param Comparator|null                             $comparator
     */
    public function __construct(array $map, Comparator $comparator = null)
    {
        if (null === $comparator) {
            $comparator = new Comparator;
        }

        foreach ($map as $tuple) {
            list($pointer, $uri) = $tuple;
            $this->add($pointer, $uri);
        }

        $this->comparator = $comparator;
    }

    /**
     * @return array<tuple<PointerInterface,UriInterface>>
     */
    public function map()
    {
        return $this->map;
    }

    /**
     * @return Comparator
     */
    public function comparator()
    {
        return $this->comparator;
    }

    /**
     * @param PointerInterface $pointer
     *
     * @return UriInterface|null
     */
    public function uriByPointer(PointerInterface $pointer)
    {
        foreach ($this->map() as $tuple) {
            list($thisPointer, $uri) = $tuple;
            if ($this->comparator()->equals($thisPointer, $pointer)) {
                return $uri;
            }
        }

        return null;
    }

    /**
     * @param PointerInterface $pointer
     * @param UriInterface     $uri
     */
    protected function add(PointerInterface $pointer, UriInterface $uri)
    {
        $this->map[] = array($pointer, $uri);
    }

    private $map;
    private $comparator;
}
