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
use Eloquent\Schemer\Pointer\Resolver\PointerResolver;
use Eloquent\Schemer\Pointer\Resolver\PointerResolverInterface;
use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Reader\ReaderInterface;
use Eloquent\Schemer\Uri\Resolver\UriResolver;
use Eloquent\Schemer\Uri\Resolver\UriResolverInterface;
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Value;
use LogicException;
use Zend\Uri\UriInterface;

class ScopeMappedReferenceResolver extends Value\Transform\AbstractValueTransform
{
    /**
     * @param UriInterface                        $baseUri
     * @param ResolutionScopeMapperInterface|null $scopeMapper
     * @param UriResolverInterface|null           $uriResolver
     * @param ReaderInterface|null                $reader
     * @param UriFactoryInterface|null            $uriFactory
     * @param PointerFactoryInterface|null        $pointerFactory
     * @param PointerResolverInterface|null       $pointerResolver
     * @param Value\ValueTransformInterface|null  $placeholderUnwrap
     */
    public function __construct(
        UriInterface $baseUri,
        ResolutionScopeMapperInterface $scopeMapper = null,
        UriResolverInterface $uriResolver = null,
        ReaderInterface $reader = null,
        UriFactoryInterface $uriFactory = null,
        PointerFactoryInterface $pointerFactory = null,
        PointerResolverInterface $pointerResolver = null,
        Value\ValueTransformInterface $placeholderUnwrap = null
    ) {
        parent::__construct();

        if (null === $scopeMapper) {
            $scopeMapper = new ResolutionScopeMapperInterface;
        }
        if (null === $uriResolver) {
            $uriResolver = new UriResolver;
        }
        if (null === $reader) {
            $reader = new Reader;
        }
        if (null === $uriFactory) {
            $uriFactory = new UriFactory;
        }
        if (null === $pointerFactory) {
            $pointerFactory = new PointerFactory;
        }
        if (null === $pointerResolver) {
            $pointerResolver = new PointerResolver;
        }
        if (null === $placeholderUnwrap) {
            $placeholderUnwrap = new Value\Transform\PlaceholderUnwrapTransform;
        }

        $this->baseUri = $baseUri;
        $this->scopeMapper = $scopeMapper;
        $this->uriResolver = $uriResolver;
        $this->reader = $reader;
        $this->uriFactory = $uriFactory;
        $this->pointerFactory = $pointerFactory;
        $this->pointerResolver = $pointerResolver;
        $this->placeholderUnwrap = $placeholderUnwrap;
    }

    /**
     * @return UriInterface
     */
    public function baseUri()
    {
        return $this->baseUri;
    }

    /**
     * @return ResolutionScopeMapperInterface
     */
    public function scopeMapper()
    {
        return $this->scopeMapper;
    }

    /**
     * @return UriResolverInterface
     */
    public function uriResolver()
    {
        return $this->uriResolver;
    }

    /**
     * @return Reader
     */
    public function reader()
    {
        return $this->reader;
    }

    /**
     * @return UriFactoryInterface
     */
    public function uriFactory()
    {
        return $this->uriFactory;
    }

    /**
     * @return PointerFactoryInterface
     */
    public function pointerFactory()
    {
        return $this->pointerFactory;
    }

    /**
     * @return PointerResolverInterface
     */
    public function pointerResolver()
    {
        return $this->pointerResolver;
    }

    /**
     * @return Value\ValueTransformInterface
     */
    public function placeholderUnwrap()
    {
        return $this->placeholderUnwrap;
    }

    /**
     * @param Value\ValueInterface $value
     *
     * @return Value\ConcreteValueInterface
     */
    public function transform(Value\ValueInterface $value)
    {
        return $this->placeholderUnwrap()->transform(parent::transform($value));
    }

    /**
     * @param Value\ReferenceValue $reference
     *
     * @return Value\PlaceholderValue
     * @throws Exception\UndefinedReferenceException
     */
    public function visitReferenceValue(Value\ReferenceValue $reference)
    {
        // resolve reference against current base URI and normalize
        $referenceUri = $this->uriResolver()->resolve(
            $this->currentBaseUri(),
            $reference->uri()
        );

        // if in-progress resolution exists
        if ($this->hasResolution($referenceUri)) {
            // return it
            return $this->resolution($referenceUri);
        }
        // start new resolution
        $resolution = $this->startResolution($referenceUri);

        // if reference is a child of any inline scope throughout the stack
        // resolve inline
        if (!$value = $this->resolveInline($referenceUri)) {
            // else resolve externally
            $value = $this->resolveExternal($referenceUri);
        }

        // complete resolution
        $this->completeResolution($referenceUri, $value);

        // return resolution
        return $resolution;
    }

    /**
     * @param UriInterface $referenceUri
     *
     * @return Value\ValueInterface|null
     * @throws Exception\UndefinedReferenceException
     */
    protected function resolveInline(UriInterface $referenceUri)
    {
        // pull relevant inline value from stack
        foreach (array_reverse($this->scopeMapStack()) as $scopeMap) {
            if
        }

        // push the relevant scope map onto the stack
        // visit the value
        // pop the scope map stack
        // resolve pointer

        // return the visited value
    }

    /**
     * @param UriInterface $referenceUri
     *
     * @return Value\ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    protected function resolveExternal(UriInterface $referenceUri)
    {
        // read external value
        // create its scope map
        // push scope map onto the stack
        // visit the value
        // pop the scope map stack
        // resolve pointer

        // return the visited value
    }

    /**
     * @param UriInterface         $referenceUri
     * @param Value\ValueInterface $value
     *
     * @return Value\ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    protected function resolveUriFragmentPointer(
        UriInterface $referenceUri,
        Value\ValueInterface $value
    ) {
        if (null !== $referenceUri->getFragment()) {
            $pointer = $this->pointerFactory()->create(
                $referenceUri->getFragment()
            );

            $value = $this->pointerResolver()->resolve($pointer, $value);
            if (null === $value) {
                throw new Exception\UndefinedReferenceException($referenceUri);
            }
        }

        return $value;
    }

    protected function clear()
    {
        parent::clear();

        $this->scopeMapStack = array();
        $this->resolutions = array();
    }

    protected function initialize(Value\ValueInterface $value)
    {
        parent::initialize($value);

        $this->pushScopeMap(
            $this->scopeMapper()->create(
                $this->baseUri(),
                $this->value()
            )
        );
    }

    /**
     * @param ResolutionScopeMap $scopeMap
     */
    protected function pushScopeMap(ResolutionScopeMap $scopeMap)
    {
        array_push($this->scopeMapStack, $scopeMap);
    }

    protected function popScopeMap()
    {
        if (count($this->scopeMapStack) < 1) {
            throw new LogicException('Scope map stack is empty.');
        }

        array_pop($this->scopeMapStack);
    }

    /**
     * @return ResolutionScopeMap
     */
    protected function currentScopeMap()
    {
        if (count($this->scopeMapStack) < 1) {
            throw new LogicException('Scope map stack is empty.');
        }

        return $this->scopeMapStack[count($this->scopeMapStack) - 1];
    }

    /**
     * @return array<ResolutionScopeMap>
     */
    protected function scopeMapStack()
    {
        return $this->scopeMapStack;
    }

    /**
     * @return UriInterface
     */
    protected function currentBaseUri()
    {
        return $this
            ->currentScopeMap()
            ->uriByPointer($this->pointerFactory()->create());
    }

    /**
     * @param UriInterface $referenceUri
     *
     * @return Value\PlaceholderValue
     */
    protected function startResolution(UriInterface $referenceUri)
    {
        $resolution = new Value\PlaceholderValue;
        $this->resolutions[$referenceUri->toString()] = $resolution;

        return $resolution;
    }

    /**
     * @param UriInterface         $referenceUri
     * @param Value\ValueInterface $value
     */
    protected function completeResolution(
        UriInterface $referenceUri,
        Value\ValueInterface $value
    ) {
        $this->resolution($referenceUri)->setInnerValue($value);
    }

    /**
     * @param UriInterface $referenceUri
     *
     * @return boolean
     */
    protected function hasResolution(UriInterface $referenceUri)
    {
        return array_key_exists(
            $referenceUri->toString(),
            $this->resolutions
        );
    }

    /**
     * @param UriInterface $referenceUri
     *
     * @return Value\ValueInterface
     */
    protected function resolution(UriInterface $referenceUri)
    {
        if (!$this->hasResolution($referenceUri)) {
            throw new LogicException('Undefined resolution.');
        }

        return $this->resolutions[$referenceUri->toString()];
    }

    private $baseUri;
    private $scopeMapper;
    private $uriResolver;
    private $reader;
    private $uriFactory;
    private $pointerFactory;
    private $pointerResolver;
    private $placeholderUnwrap;

    private $scopeMapStack;
    private $resolutions;
}
