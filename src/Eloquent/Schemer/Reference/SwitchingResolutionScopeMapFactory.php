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
use Eloquent\Schemer\Uri\Resolver\UriResolver;
use Eloquent\Schemer\Uri\Resolver\UriResolverInterface;
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Value;
use Zend\Uri\UriInterface;

class SwitchingResolutionScopeMapFactory extends Value\Visitor\AbstractValueVisitor implements
    ResolutionScopeMapFactoryInterface
{
    /**
     * @param string|null                  $propertyName
     * @param UriFactoryInterface|null     $uriFactory
     * @param UriResolverInterface|null    $uriResolver
     * @param PointerFactoryInterface|null $pointerFactory
     * @param Comparator|null              $comparator
     */
    public function __construct(
        $propertyName = null,
        UriFactoryInterface $uriFactory = null,
        UriResolverInterface $uriResolver = null,
        PointerFactoryInterface $pointerFactory = null,
        Comparator $comparator = null
    ) {
        if (null === $propertyName) {
            $propertyName = 'id';
        }
        if (null === $uriFactory) {
            $uriFactory = new UriFactory;
        }
        if (null === $uriResolver) {
            $uriResolver = new UriResolver;
        }
        if (null === $pointerFactory) {
            $pointerFactory = new PointerFactory;
        }
        if (null === $comparator) {
            $comparator = new Comparator;
        }

        $this->propertyName = $propertyName;
        $this->uriFactory = $uriFactory;
        $this->uriResolver = $uriResolver;
        $this->pointerFactory = $pointerFactory;
        $this->comparator = $comparator;

        $this->clear();
    }

    /**
     * @return string
     */
    public function propertyName()
    {
        return $this->propertyName;
    }

    /**
     * @return UriFactoryInterface
     */
    public function uriFactory()
    {
        return $this->uriFactory;
    }

    /**
     * @return UriResolverInterface
     */
    public function uriResolver()
    {
        return $this->uriResolver;
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
     * @param UriInterface         $baseUri
     * @param Value\ValueInterface $value
     *
     * @return ResolutionScopeMap
     */
    public function create(UriInterface $baseUri, Value\ValueInterface $value)
    {
        $this->clear();
        $this->pushBaseUri($baseUri);
        $this->addMapping($this->currentPointer(), $baseUri);

        $value->accept($this);
        $map = new ResolutionScopeMap($this->map(), $value);

        $this->clear();

        return $map;
    }

    /**
     * @param Value\ArrayValue $value
     */
    public function visitArrayValue(Value\ArrayValue $value)
    {
        foreach ($value as $index => $subValue) {
            $this->pushPointerAtom(strval($index));
            $subValue->accept($this);
            $this->popPointer();
        }
    }

    /**
     * @param Value\ObjectValue $value
     */
    public function visitObjectValue(Value\ObjectValue $value)
    {
        foreach ($value as $property => $subValue) {
            if ($this->propertyName() === $property) {
                if (!$subValue instanceof Value\StringValue) {
                    continue;
                }

                $this->pushBaseUriReference(
                    $this->uriFactory()->createGeneric($subValue->value())
                );
                $this->addMapping(
                    $this->currentPointer(),
                    $this->currentBaseUri()
                );
            }

            $this->pushPointerAtom($property);
            $subValue->accept($this);
            $this->popPointer();
        }
    }

    protected function clear()
    {
        $this->baseUriStack = array();
        $this->pointerStack = array($this->pointerFactory()->create());
        $this->map = array();
    }

    /**
     * @param UriInterface $baseUri
     */
    protected function pushBaseUri(UriInterface $baseUri)
    {
        array_push($this->baseUriStack, $baseUri);
    }

    /**
     * @param UriInterface $baseUri
     */
    protected function pushBaseUriReference(UriInterface $baseUri)
    {
        $this->pushBaseUri(
            $this->uriResolver()->resolve($baseUri, $this->currentBaseUri())
        );
    }

    protected function popBaseUri()
    {
        if (count($this->baseUriStack) < 1) {
            throw new LogicException('Base URI stack is empty.');
        }

        array_pop($this->baseUriStack);
    }

    /**
     * @return UriInterface
     */
    protected function currentBaseUri()
    {
        if (count($this->baseUriStack) < 1) {
            throw new LogicException('Base URI stack is empty.');
        }

        return $this->baseUriStack[count($this->baseUriStack) - 1];
    }

    /**
     * @param string $atom
     */
    protected function pushPointerAtom($atom)
    {
        array_push($this->pointerStack, $this->currentPointer()->joinAtoms($atom));
    }

    protected function popPointer()
    {
        if (count($this->pointerStack) < 1) {
            throw new LogicException('Pointer stack is empty.');
        }

        array_pop($this->pointerStack);
    }

    /**
     * @return PointerInterface
     */
    protected function currentPointer()
    {
        if (count($this->pointerStack) < 1) {
            throw new LogicException('Pointer stack is empty.');
        }

        return $this->pointerStack[count($this->pointerStack) - 1];
    }

    /**
     * @param PointerInterface $pointer
     * @param UriInterface     $uri
     */
    protected function addMapping(PointerInterface $pointer, UriInterface $uri)
    {
        foreach ($this->map as $index => $tuple) {
            list($existingPointer) = $tuple;
            if ($this->comparator->equals($existingPointer, $pointer)) {
                $this->map[$index] = array($pointer, $uri);

                return;
            }
        }

        $this->map[] = array($pointer, $uri);
    }

    /**
     * @return array<tuple<UriInterface,PointerInterface>>
     */
    protected function map()
    {
        return $this->map;
    }

    private $propertyName;
    private $uriFactory;
    private $uriResolver;
    private $pointerFactory;
    private $comparator;

    private $baseUriStack;
    private $pointerStack;
    private $map;
}
