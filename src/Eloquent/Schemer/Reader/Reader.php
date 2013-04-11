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

use Eloquent\Schemer\Loader\ContentType;
use Eloquent\Schemer\Loader\Loader;
use Eloquent\Schemer\Loader\LoaderInterface;
use Eloquent\Schemer\Serialization\ProtocolMap;
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Value\Factory\ValueFactory;
use Eloquent\Schemer\Value\Factory\ValueFactoryInterface;
use Zend\Uri\UriInterface;

class Reader implements ReaderInterface
{
    /**
     * @param LoaderInterface|null       $loader
     * @param ProtocolMap|null           $protocolMap
     * @param ValueFactoryInterface|null $valueFactory
     * @param UriFactoryInterface|null   $uriFactory
     */
    public function __construct(
        LoaderInterface $loader = null,
        ProtocolMap $protocolMap = null,
        ValueFactoryInterface $valueFactory = null,
        UriFactoryInterface $uriFactory = null
    ) {
        if (null === $loader) {
            $loader = new Loader;
        }
        if (null === $protocolMap) {
            $protocolMap = new ProtocolMap;
        }
        if (null === $uriFactory) {
            $uriFactory = new UriFactory;
        }
        if (null === $valueFactory) {
            $valueFactory = new ValueFactory($uriFactory);
        }

        $this->loader = $loader;
        $this->protocolMap = $protocolMap;
        $this->valueFactory = $valueFactory;
        $this->uriFactory = $uriFactory;
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
     * @return ValueFactoryInterface
     */
    public function valueFactory()
    {
        return $this->valueFactory;
    }

    /**
     * @return UriFactoryInterface
     */
    public function uriFactory()
    {
        return $this->uriFactory;
    }

    /**
     * @param \Zend\Uri\UriInterface|string $uri
     * @param string|null                   $mimeType
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
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

    /**
     * @param string      $path
     * @param string|null $mimeType
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readPath($path, $mimeType = null)
    {
        return $this->read($this->uriFactory()->fromPath($path), $mimeType);
    }

    /**
     * @param string      $data
     * @param string|null $mimeType
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readString($data, $mimeType = null)
    {
        if (null === $mimeType) {
            $mimeType = ContentType::JSON()->primaryMimeType();
        }

        return $this->read($this->uriFactory()->fromData($data, $mimeType), $mimeType);
    }

    private $loader;
    private $protocolMap;
    private $valueFactory;
    private $uriFactory;
}
