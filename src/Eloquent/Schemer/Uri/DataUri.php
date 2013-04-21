<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri;

use Zend\Uri\Exception\InvalidArgumentException;
use Zend\Uri\Exception\InvalidUriException;
use Zend\Uri\UriInterface;

class DataUri extends Uri
{
    protected static $validSchemes = array('data');

    /**
     * @param string|UriInterface|null $uri
     */
    public function __construct($uri = null)
    {
        $this->setScheme('data');
        $this->setEncoding('base64');

        if ($uri instanceof self) {
            $this->setMimeType($uri->getMimeType());
            $this->setEncoding($uri->getEncoding());
            $this->setRawData($uri->getRawData());
        } elseif (is_string($uri)) {
            $this->parse($uri);
        } elseif ($uri instanceof UriInterface) {
            $this->parse($uri->toString());
        } elseif ($uri !== null) {
            throw new InvalidArgumentException(sprintf(
                'Expecting a string or a URI object, received "%s"',
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }
    }

    /**
     * @param string|null $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        if (empty($this->mimeType)) {
            return 'text/plain;charset=US-ASCII';
        }

        return $this->mimeType;
    }

    /**
     * @param string|null $encoding
     */
    public function setEncoding($encoding)
    {
        $isDifferent = $this->encoding !== $encoding;
        if ($isDifferent) {
            $data = $this->getData();
        }

        $this->encoding = $encoding;

        if ($isDifferent) {
            $this->setData($data);
        }
    }

    /**
     * @return string|null
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return string
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        if ('base64' === $this->encoding) {
            $this->setRawData(base64_encode($data));
        } else {
            $this->setRawData(rawurlencode($data));
        }
    }

    /**
     * @return string
     */
    public function getData()
    {
        if ('base64' === $this->encoding) {
            return base64_decode($this->getRawData());
        }

        return rawurldecode($this->getRawData());
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        if (
            !empty($this->mimeType) &&
            !preg_match(
                '#^[\w-]+/[\w-]+(?:;\s*[\w-]+=[\w-]+)*$#',
                $this->mimeType
            )
        ) {
            return false;
        }

        if (!empty($this->encoding) && 'base64' !== $this->encoding) {
            return false;
        }

        if (
            'base64' === $this->encoding &&
            !preg_match(
                '#^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?$#',
                $this->rawData
            )
        ) {
            return false;
        } elseif (!preg_match('/^[!#$&-;=?-[\]_a-z~%]+$/', $this->rawData)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $uri
     *
     * @return DataUri
     */
    public function parse($uri)
    {
        if (($scheme = static::parseScheme($uri)) !== null) {
            $this->setScheme($scheme);
            $uri = substr($uri, strlen($scheme) + 1);
        }

        $parts = explode(',', $uri, 2);

        if (count($parts) > 1) {
            $mimeType = array_shift($parts);
            if (';base64' === substr($mimeType, -7)) {
                $this->encoding = 'base64';
                $mimeType = substr($mimeType, 0, -7);
            }
            $this->setMimeType($mimeType);
        }

        $this->rawData = array_shift($parts);

        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->isValid()) {
            throw new InvalidUriException(
                'URI is not valid and cannot be converted into a string'
            );
        }

        $uri = '';
        if ($this->scheme) {
            $uri .= $this->scheme . ':';
        }
        if ($this->mimeType) {
            $uri .= $this->mimeType;
        }
        if ($this->encoding) {
            $uri .= ';' . $this->encoding;
        }
        $uri .= ',' . $this->rawData;

        return $uri;
    }

    /**
     * @return DataUri
     */
    public function normalize()
    {
        if ($this->scheme) {
            $this->scheme = static::normalizeScheme($this->scheme);
        }

        if ($this->encoding) {
            $this->encoding = static::normalizeEncoding($this->encoding);
        }

        return $this;
    }

    /**
     * @param string $encoding
     *
     * @return string
     */
    protected static function normalizeEncoding($encoding)
    {
        if (preg_match('//', $encoding)) {
            return null;
        }

        return $encoding;
    }

    protected $mimeType;
    protected $encoding;
    protected $rawData;
}
