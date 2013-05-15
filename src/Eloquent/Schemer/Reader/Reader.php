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

use Eloquent\Schemer\Loader\Loader;
use Eloquent\Schemer\Loader\LoaderInterface;
use Eloquent\Schemer\Serialization\ProtocolMap;
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Value;
use Zend\Uri\UriInterface;

class Reader extends AbstractReader
{
    /**
     * @param LoaderInterface|null                     $loader
     * @param ProtocolMap|null                         $protocolMap
     * @param Value\Factory\ValueFactoryInterface|null $valueFactory
     * @param UriFactoryInterface|null                 $uriFactory
     */
    public function __construct(
        LoaderInterface $loader = null,
        ProtocolMap $protocolMap = null,
        Value\Factory\ValueFactoryInterface $valueFactory = null,
        UriFactoryInterface $uriFactory = null
    ) {
        parent::__construct($uriFactory);

        if (null === $loader) {
            $loader = new Loader;
        }
        if (null === $protocolMap) {
            $protocolMap = new ProtocolMap;
        }
        if (null === $valueFactory) {
            $valueFactory = new Value\Factory\ValueFactory($this->uriFactory());
        }

        $this->loader = $loader;
        $this->protocolMap = $protocolMap;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @return LoaderInterface
     */
    public function loader()
    {
        return $this->loader;
    }

    /**
     * @return ProtocolMap
     */
    public function protocolMap()
    {
        return $this->protocolMap;
    }

    /**
     * @return Value\Factory\ValueFactoryInterface
     */
    public function valueFactory()
    {
        return $this->valueFactory;
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

        $content = $this->loader()->load($uri);
        if (null === $mimeType) {
            $mimeType = $content->mimeType();
        }

        return $this->valueFactory()->create(
            $this->protocolMap()->get($mimeType)->thaw($content->data())
        );
    }

    private $loader;
    private $protocolMap;
    private $valueFactory;
}
