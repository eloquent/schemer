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
        $uri = $value->reference();
        if (!$uri->isAbsolute()) {
            $uri = $this->uriResolver()->resolve($uri);
        }
        $pointer = $value->pointer();

        try {
            $value = $this->reader()->read($uri, $value->mimeType());
        } catch (ReadException $e) {
            throw new Exception\UndefinedReferenceException(
                $value,
                $this->uriResolver()->baseUri(),
                $e
            );
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

    private $uriResolver;
    private $reader;
    private $pointerResolver;
}
