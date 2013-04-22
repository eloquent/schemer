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

use Eloquent\Schemer\Pointer\PointerFactory;
use Eloquent\Schemer\Pointer\PointerFactoryInterface;
use Eloquent\Schemer\Pointer\PointerInterface;
use Eloquent\Schemer\Uri\Resolver\UriResolver;
use Eloquent\Schemer\Uri\Resolver\UriResolverInterface;
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Value;
use Zend\Uri\UriInterface;

class ResolutionScopeMapper extends Value\Visitor\AbstractValueVisitor implements
    ResolutionScopeMapperInterface
{
    /**
     * @param UriFactoryInterface|null     $uriFactory
     * @param UriResolverInterface|null    $uriResolver
     * @param PointerFactoryInterface|null $pointerFactory
     */
    public function __construct(
        UriFactoryInterface $uriFactory = null,
        UriResolverInterface $uriResolver = null,
        PointerFactoryInterface $pointerFactory = null
    ) {
        if (null === $uriFactory) {
            $uriFactory = new UriFactory;
        }
        if (null === $uriResolver) {
            $uriResolver = new UriResolver;
        }
        if (null === $pointerFactory) {
            $pointerFactory = new PointerFactory;
        }

        $this->uriFactory = $uriFactory;
        $this->uriResolver = $uriResolver;
        $this->pointerFactory = $pointerFactory;

        $this->clear();
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
        $map = new ResolutionScopeMap($this->map());

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
            if ('id' === $property) {
                if (!$subValue instanceof Value\StringValue) {
                    throw new RuntimeException('Invalid resolution scope.');
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
        array_push($this->pointerStack, $this->currentPointer()->joinAtom($atom));
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
        $this->map[] = array($pointer, $uri);
    }

    /**
     * @return array<tuple<UriInterface,PointerInterface>>
     */
    protected function map()
    {
        return $this->map;
    }

    private $uriFactory;
    private $uriResolver;
    private $pointerFactory;

    private $baseUriStack;
    private $pointerStack;
    private $map;
}
