<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reader;

use Eloquent\Schemer\Reference\ReferenceResolverFactoryInterface;
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Value;
use Zend\Uri\UriInterface;

abstract class AbstractResolvingReader extends AbstractReader
{
    /**
     * @param ReferenceResolverFactoryInterface $resolverFactory
     * @param ReaderInterface|null              $reader
     * @param UriFactoryInterface|null          $uriFactory
     */
    public function __construct(
        ReferenceResolverFactoryInterface $resolverFactory,
        ReaderInterface $reader = null,
        UriFactoryInterface $uriFactory = null
    ) {
        parent::__construct($uriFactory);

        if (null === $reader) {
            $reader = new Reader;
        }

        $this->resolverFactory = $resolverFactory;
        $this->reader = $reader;
    }

    /**
     * @return ReferenceResolverFactoryInterface
     */
    public function resolverFactory()
    {
        return $this->resolverFactory;
    }

    /**
     * @return ReaderInterface
     */
    public function reader()
    {
        return $this->reader;
    }

    /**
     * @param \Zend\Uri\UriInterface|string $uri
     * @param string|null                   $mimeType
     *
     * @return Value\ValueInterface
     */
    public function read($uri, $mimeType = null)
    {
        if (!$uri instanceof UriInterface) {
            $uri = $this->uriFactory()->create($uri);
        }
        $resolver = $this->resolverFactory()->create($uri);

        return $resolver->transform(
            $this->reader()->read($uri, $mimeType)
        );
    }

    private $resolverFactory;
    private $reader;
}
