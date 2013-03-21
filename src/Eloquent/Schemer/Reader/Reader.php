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
use Eloquent\Schemer\Value\Transform\ValueTransform;
use Eloquent\Schemer\Value\Transform\ValueTransformInterface;
use Zend\Uri\UriInterface;

class Reader implements ReaderInterface
{
    /**
     * @param LoaderInterface|null         $loader
     * @param ProtocolMap|null             $protocolMap
     * @param ValueTransformInterface|null $transform
     * @param UriFactoryInterface|null     $uriFactory
     */
    public function __construct(
        LoaderInterface $loader = null,
        ProtocolMap $protocolMap = null,
        ValueTransformInterface $transform = null,
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
        if (null === $transform) {
            $transform = new ValueTransform($uriFactory);
        }

        $this->loader = $loader;
        $this->protocolMap = $protocolMap;
        $this->transform = $transform;
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
     * @return ValueTransformInterface
     */
    public function transform()
    {
        return $this->transform;
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
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function read($uri)
    {
        if (!$uri instanceof UriInterface) {
            $uri = $this->uriFactory()->create($uri);
        }

        $content = $this->loader()->load($uri);

        return $this->transform()->apply(
            $this->protocolMap()->get($content->type())->thaw($content->data())
        );
    }

    /**
     * @param string $path
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readPath($path)
    {
        return $this->read($this->uriFactory()->fromPath($path));
    }

    /**
     * @param string      $data
     * @param string|null $type
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readString($data, $type = null)
    {
        if (null === $type) {
            $type = ContentType::JSON()->primaryType();
        }

        return $this->read($this->uriFactory()->fromData($data, $type));
    }

    private $loader;
    private $protocolMap;
    private $transform;
    private $uriFactory;
}
