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

use Eloquent\Schemer\Pointer\PointerInterface;
use Zend\Uri\UriInterface;

class ResolutionScopeMap
{
    /**
     * @param array<tuple<UriInterface,PointerInterface>> $map
     */
    public function __construct(array $map)
    {
        foreach ($map as $tuple) {
            list($uri, $pointer) = $tuple;
            $this->add($uri, $pointer);
        }
    }

    /**
     * @return array<string,PointerInterface>
     */
    public function map()
    {
        return $this->map;
    }

    /**
     * @param UriInterface $uri
     *
     * @return PointerInterface|null
     */
    public function get(UriInterface $uri)
    {
        $key = $this->normalizeUri($uri)->toString();
        if (array_key_exists($key, $this->map)) {
            return $this->map[$key];
        }

        return null;
    }

    /**
     * @param UriInterface     $uri
     * @param PointerInterface $pointer
     */
    protected function add(UriInterface $uri, PointerInterface $pointer)
    {
        $this->map[$this->normalizeUri($uri)->toString()] = $pointer;
    }

    /**
     * @param UriInterface $uri
     *
     * @return UriInterface
     */
    protected function normalizeUri(UriInterface $uri)
    {
        $uri = clone $uri;
        $uri->normalize();

        return $uri;
    }

    private $map;
}
