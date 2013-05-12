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
use Eloquent\Schemer\Pointer\PointerFactory;
use Eloquent\Schemer\Pointer\PointerFactoryInterface;
use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Value;
use LogicException;
use Zend\Uri\UriInterface;

class ResolutionScopeMap
{
    /**
     * @param array<tuple<PointerInterface,UriInterface>> $map
     * @param Value\ValueInterface                        $value
     * @param PointerFactoryInterface|null                $pointerFactory
     * @param Comparator|null                             $comparator
     */
    public function __construct(
        array $map,
        Value\ValueInterface $value,
        PointerFactoryInterface $pointerFactory = null,
        Comparator $comparator = null
    ) {
        if (null === $pointerFactory) {
            $pointerFactory = new PointerFactory;
        }
        if (null === $comparator) {
            $comparator = new Comparator;
        }

        foreach ($map as $tuple) {
            list($pointer, $uri) = $tuple;
            $this->add($pointer, $uri);
        }

        $this->value = $value;
        $this->pointerFactory = $pointerFactory;
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
     * @return Value\ValueInterface
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return PointerFactoryInterface
     */
    public function pointerFactory()
    {
        return $this->pointerFactory;
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
     * @return UriInterface
     */
    public function uriByPointer(PointerInterface $pointer)
    {
        foreach ($this->map() as $tuple) {
            list($mappingPointer, $mappingUri) = $tuple;
            if ($this->comparator()->equals($mappingPointer, $pointer)) {
                return $mappingUri;
            }
        }

        throw new LogicException(
            sprintf('No URI defined for pointer "%s"', $pointer->string())
        );
    }

    /**
     * @param UriInterface $uri
     *
     * @return PointerInterface|null
     */
    public function pointerByUri(UriInterface $uri)
    {
        $uriPointerMap = $this->createUriPointerMap($uri);

        foreach ($this->map() as $tuple) {
            list($mappingPointer, $mappingUri) = $tuple;
            $mappingUriString = $mappingUri->toString();

            foreach ($uriPointerMap as $tuple) {
                list($uriString, $atoms) = $tuple;

                if ($uriString === $mappingUriString) {
                    return $mappingPointer->joinAtomSequence($atoms);
                }
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
        $uri = clone $uri;
        $uri->normalize();

        $this->map[] = array($pointer, $uri);
    }

    /**
     * Splits a URI into a form that makes it easier to match its parents with
     * regards to URIs that contain pointers in their fragments.
     *
     * @param UriInterface $uri
     *
     * @return array<tuple<string,array<string>>>
     */
    protected function createUriPointerMap(UriInterface $uri)
    {
        $uri = clone $uri;
        $uri->normalize();
        $map = array(array($uri->toString(), array()));

        if (
            null !== $uri->getFragment() &&
            '/' === substr($uri->getFragment(), 0, 1)
        ) {
            $atoms = array();
            $fragmentPointer = $this->pointerFactory()
                ->create($uri->getFragment());
            $fragmentlessUri = clone $uri;
            $fragmentlessUri->setFragment(null);

            while ($fragmentPointer->hasAtoms()) {
                array_unshift($atoms, $fragmentPointer->lastAtom());
                $fragmentPointer = $fragmentPointer->parent();

                $uri = clone $fragmentlessUri;
                $uri->setFragment($fragmentPointer->string());
                $uri->normalize();

                array_unshift($map, array($uri->toString(), $atoms));
            }
        }

        return $map;
    }

    private $map;
    private $value;
    private $pointerFactory;
    private $comparator;
}
