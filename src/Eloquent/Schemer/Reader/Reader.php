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
use Eloquent\Schemer\Uri\DataUri;
use Eloquent\Schemer\Value\Transform\ValueTransform;
use Eloquent\Schemer\Value\Transform\ValueTransformInterface;
use Icecave\Isolator\Isolator;
use Zend\Uri\File as FileUri;
use Zend\Uri\UriFactory;
use Zend\Uri\UriInterface;

class Reader implements ReaderInterface
{
    public function __construct(
        LoaderInterface $loader = null,
        ProtocolMap $protocolMap = null,
        ValueTransformInterface $transform = null,
        Isolator $isolator = null
    ) {
        if (null === $loader) {
            $loader = new Loader;
        }
        if (null === $protocolMap) {
            $protocolMap = new ProtocolMap;
        }
        if (null === $transform) {
            $transform = new ValueTransform;
        }

        $this->loader = $loader;
        $this->protocolMap = $protocolMap;
        $this->transform = $transform;
        $this->isolator = Isolator::get($isolator);
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
     * @param \Zend\Uri\UriInterface|string $uri
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function read($uri)
    {
        if (!$uri instanceof UriInterface) {
            $uri = UriFactory::factory($uri);
            if (!$uri instanceof DataUri && 'data' === $uri->getScheme()) {
                $uri = new DataUri($uri);
            }
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
        if ($this->isolator->defined('PHP_WINDOWS_VERSION_BUILD')) {
            $uri = FileUri::fromWindowsPath($path);
        } else {
            $uri = FileUri::fromUnixPath($path);
        }

        return $this->read($uri);
    }

    /**
     * @param string $data
     * @param string|null $type
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readString($data, $type = null)
    {
        if (null === $type) {
            $type = ContentType::JSON()->primaryType();
        }

        $uri = new DataUri;
        $uri->setMimeType($type);
        $uri->setData($data);

        return $this->read($uri);
    }

    private $loader;
    private $protocolMap;
    private $transform;
    private $isolator;
}
