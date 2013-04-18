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

class ReferenceResolver extends Value\Transform\AbstractValueTransform
{
    /**
     * @param UriInterface                  $baseUri
     * @param UriResolverInterface|null     $uriResolver
     * @param ReaderInterface|null          $reader
     * @param UriFactoryInterface|null      $uriFactory
     * @param PointerFactoryInterface|null  $pointerFactory
     * @param PointerResolverInterface|null $pointerResolver
     */
    public function __construct(
        UriInterface $baseUri,
        UriResolverInterface $uriResolver = null,
        ReaderInterface $reader = null,
        UriFactoryInterface $uriFactory = null,
        PointerFactoryInterface $pointerFactory = null,
        PointerResolverInterface $pointerResolver = null
    ) {
        parent::__construct();

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

        $this->baseUri = $baseUri;
        $this->uriResolver = $uriResolver;
        $this->reader = $reader;
        $this->uriFactory = $uriFactory;
        $this->pointerFactory = $pointerFactory;
        $this->pointerResolver = $pointerResolver;

        $this->baseUriStack = array();
        $this->resolutions = array();
    }

    /**
     * @return UriInterface
     */
    public function baseUri()
    {
        return $this->baseUri;
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
     * @param Value\ReferenceValue $reference
     *
     * @return Value\ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    public function visitReferenceValue(Value\ReferenceValue $reference)
    {
        if ($this->hasResolution($reference)) {
            $resolution = $this->resolution($reference);
            if (null === $resolution) {
                throw new LogicException('Unresolvable recursive reference.');
            }

            return $resolution;
        }
        $this->startResolution($reference);

        // use scheme-specific URI instances
        $referenceUri = $this->uriFactory()->create(
            $reference->uri()->toString()
        );
        if (!$referenceUri->isAbsolute()) {
            $referenceUri = $this->uriResolver()->resolve(
                $referenceUri,
                $this->currentBaseUri()
            );
        }
        $referenceUri->normalize();

        if ($referenceUri->toString() === $this->currentBaseUri()->toString()) {
            $value = $this->resolveInline($reference);
        } else {
            $value = $this->resolveExternal($reference, $referenceUri);
        }

        $value = $this->resolvePointer($reference, $value);
        $this->completeResolution($reference, $value);

        return $value;
    }

    /**
     * @param Value\ReferenceValue $reference
     *
     * @return Value\ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    protected function resolveInline(Value\ReferenceValue $reference)
    {
        return $this->value()->accept($this);
    }

    /**
     * @param Value\ReferenceValue $reference
     * @param UriInterface         $referenceUri
     *
     * @return Value\ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    protected function resolveExternal(
        Value\ReferenceValue $reference,
        UriInterface $referenceUri
    ) {
        try {
            $value = $this->reader()->read($referenceUri, $reference->mimeType());
        } catch (ReadException $e) {
            throw new Exception\UndefinedReferenceException(
                $reference,
                $this->currentBaseUri(),
                $e
            );
        }

        $this->pushBaseUri($referenceUri);
        $value = $value->accept($this);
        $this->popBaseUri();

        return $value;
    }

    /**
     * @param Value\ReferenceValue $reference
     * @param Value\ValueInterface $value
     *
     * @return Value\ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    protected function resolvePointer(
        Value\ReferenceValue $reference,
        Value\ValueInterface $value
    ) {
        if (null !== $reference->uri()->getFragment()) {
            $pointer = $this->pointerFactory()->create(
                $reference->uri()->getFragment()
            );

            $value = $this->pointerResolver()->resolve($pointer, $value);
            if (null === $value) {
                throw new Exception\UndefinedReferenceException(
                    $reference,
                    $this->currentBaseUri()
                );
            }
        }

        return $value;
    }

    /**
     * @param UriInterface $baseUri
     */
    protected function pushBaseUri(UriInterface $baseUri)
    {
        array_push($this->baseUriStack, $baseUri);
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
            return $this->baseUri;
        }

        return $this->baseUriStack[count($this->baseUriStack) - 1];
    }

    protected function clear()
    {
        parent::clear();

        $this->resolutions = array();
    }

    /**
     * @param Value\ReferenceValue $reference
     */
    protected function startResolution(Value\ReferenceValue $reference)
    {
        $this->resolutions[$reference->uri()->toString()] = null;
    }

    /**
     * @param Value\ReferenceValue $reference
     * @param Value\ValueInterface $value
     */
    protected function completeResolution(
        Value\ReferenceValue $reference,
        Value\ValueInterface $value
    ) {
        $this->resolutions[$reference->uri()->toString()] = $value;
    }

    /**
     * @param Value\ReferenceValue $reference
     *
     * @return boolean
     */
    protected function hasResolution(Value\ReferenceValue $reference)
    {
        return array_key_exists(
            $reference->uri()->toString(),
            $this->resolutions
        );
    }

    /**
     * @param Value\ReferenceValue $reference
     *
     * @return Value\ValueInterface|null
     */
    protected function resolution(Value\ReferenceValue $reference)
    {
        if (!$this->hasResolution($reference)) {
            throw new LogicException('Undefined resolution.');
        }

        return $this->resolutions[$reference->uri()->toString()];
    }

    private $baseUri;
    private $uriResolver;
    private $reader;
    private $uriFactory;
    private $pointerFactory;
    private $pointerResolver;
    private $baseUriStack;
    private $resolutions;
}
