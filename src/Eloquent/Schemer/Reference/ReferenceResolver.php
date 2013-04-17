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

use Eloquent\Schemer\Pointer\Resolver\PointerResolver;
use Eloquent\Schemer\Pointer\Resolver\PointerResolverInterface;
use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Reader\ReaderInterface;
use Eloquent\Schemer\Uri\Resolver\BoundUriResolverInterface;
use Eloquent\Schemer\Value;
use Zend\Uri\UriInterface;

class ReferenceResolver extends Value\Transform\AbstractValueTransform
{
    /**
     * @param BoundUriResolverInterface     $uriResolver
     * @param ReaderInterface|null          $reader
     * @param PointerResolverInterface|null $pointerResolver
     */
    public function __construct(
        BoundUriResolverInterface $uriResolver,
        ReaderInterface $reader = null,
        PointerResolverInterface $pointerResolver = null
    ) {
        parent::__construct();

        if (null === $reader) {
            $reader = new Reader;
        }
        if (null === $pointerResolver) {
            $pointerResolver = new PointerResolver;
        }

        $this->uriResolver = $uriResolver;
        $this->reader = $reader;
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
     * @return PointerResolverInterface
     */
    public function pointerResolver()
    {
        return $this->pointerResolver;
    }

    /**
     * @param Value\ReferenceValue $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    public function visitReferenceValue(Value\ReferenceValue $value)
    {
        $reference = $value->reference();
        if (!$reference->isAbsolute()) {
            $reference = $this->uriResolver()->resolve($reference);
            $reference->normalize();
        }
        $pointer = $value->pointer();

        if ($reference->toString() === $this->uriResolver()->baseUri()->toString()) {
            $value = $this->value();
        } else {
            $value = $this->resolveExternal($value, $reference);
        }

        if (null !== $pointer) {
            $value = $this->pointerResolver()->resolve($pointer, $value);
            if (null === $value) {
                throw new Exception\UndefinedReferenceException(
                    $value,
                    $this->uriResolver()->baseUri()
                );
            }
        }

        return $value;
    }

    /**
     * @param Value\ReferenceValue $value
     * @param UriInterface         $reference
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     * @throws Exception\UndefinedReferenceException
     */
    protected function resolveExternal(
        Value\ReferenceValue $value,
        UriInterface $reference
    ) {
        try {
            $value = $this->reader()->read($reference, $value->mimeType());
        } catch (ReadException $e) {
            throw new Exception\UndefinedReferenceException(
                $value,
                $this->uriResolver()->baseUri(),
                $e
            );
        }

        return $value;
    }

    private $uriResolver;
    private $reader;
    private $pointerResolver;
}
