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
use Eloquent\Schemer\Uri\Resolver\BoundUriResolverInterface;
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Value;
use Zend\Uri\UriInterface;

class ReferenceResolver extends Value\Transform\AbstractValueTransform
{
    /**
     * @param BoundUriResolverInterface     $uriResolver
     * @param ReaderInterface|null          $reader
     * @param UriFactoryInterface|null      $uriFactory
     * @param PointerFactoryInterface|null  $pointerFactory
     * @param PointerResolverInterface|null $pointerResolver
     */
    public function __construct(
        BoundUriResolverInterface $uriResolver,
        ReaderInterface $reader = null,
        UriFactoryInterface $uriFactory = null,
        PointerFactoryInterface $pointerFactory = null,
        PointerResolverInterface $pointerResolver = null
    ) {
        parent::__construct();

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

        $this->uriResolver = $uriResolver;
        $this->reader = $reader;
        $this->uriFactory = $uriFactory;
        $this->pointerFactory = $pointerFactory;
        $this->pointerResolver = $pointerResolver;
    }

    /**
     * @return BoundUriResolverInterface
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
        // use scheme-specific URI instances
        $referenceUri = $this->uriFactory()->create(
            $reference->uri()->toString()
        );
        if (!$referenceUri->isAbsolute()) {
            $referenceUri = $this->uriResolver()->resolve($referenceUri);
        }
        $referenceUri->normalize();

        if ($referenceUri->toString() === $this->uriResolver()->baseUri()->toString()) {
            $value = $this->value();
        } else {
            $value = $this->resolveExternal($reference, $referenceUri);
        }

        return $this->resolvePointer($reference, $value);
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
                $this->uriResolver()->baseUri(),
                $e
            );
        }

        return $value->accept($this);
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
                    $this->uriResolver()->baseUri()
                );
            }
        }

        return $value;
    }

    private $uriResolver;
    private $reader;
    private $uriFactory;
    private $pointerFactory;
    private $pointerResolver;
}
