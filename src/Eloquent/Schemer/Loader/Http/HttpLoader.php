<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader\Http;

use Buzz\Browser;
use Buzz\Message\Response;
use Eloquent\Schemer\Loader\Content;
use Eloquent\Schemer\Loader\ContentType;
use Eloquent\Schemer\Loader\Exception\InvalidUriTypeException;
use Eloquent\Schemer\Loader\Exception\LoadException;
use Eloquent\Schemer\Loader\Exception\RelativeUriException;
use Eloquent\Schemer\Loader\ExtensionTypeMap;
use Eloquent\Schemer\Loader\LoaderInterface;
use Eloquent\Schemer\Uri\HttpUriInterface;
use Eloquent\Schemer\Uri\UriInterface;

class HttpLoader implements LoaderInterface
{
    /**
     * @param ExtensionTypeMap|null $extensionMap
     * @param Browser|null          $browser
     */
    public function __construct(
        ExtensionTypeMap $extensionMap = null,
        Browser $browser = null
    ) {
        if (null === $extensionMap) {
            $extensionMap = new ExtensionTypeMap;
        }
        if (null === $browser) {
            $browser = new Browser;
        }

        $this->extensionMap = $extensionMap;
        $this->browser = $browser;
    }

    /**
     * @return ExtensionTypeMap
     */
    public function extensionMap()
    {
        return $this->extensionMap;
    }

    /**
     * @param string $mimeType
     */
    public function setDefaultMimeType($mimeType)
    {
        $this->extensionMap()->setDefaultMimeType($mimeType);
    }

    /**
     * @return string
     */
    public function defaultMimeType()
    {
        return $this->extensionMap()->defaultMimeType();
    }

    /**
     * @return Browser
     */
    public function browser()
    {
        return $this->browser;
    }

    /**
     * @param UriInterface $uri
     *
     * @return Content
     * @throws LoadException
     */
    public function load(UriInterface $uri)
    {
        if (!$uri instanceof HttpUriInterface) {
            throw new InvalidUriTypeException(
                $uri,
                'Eloquent\Schemer\Uri\HttpUriInterface'
            );
        }
        if (!$uri->isAbsolute()) {
            throw new RelativeUriException($uri);
        }

        $response = $this->browser()->get($uri->toString());
        if (!$response->isSuccessful()) {
            throw new LoadException($uri);
        }

        $mimeType = $this->mimeTypeByResponse($response);
        if (null === $mimeType) {
            $mimeType = $this->extensionMap()->getByPath(
                $this->pathFromUri($uri)
            );
        }

        return new Content($response->getContent(), $mimeType);
    }

    /**
     * @param Response $response
     *
     * @return string|null
     */
    protected function mimeTypeByResponse(Response $response)
    {
        $mimeType = $response->getHeader('Content-Type', false);
        if (count($mimeType) > 0) {
            $mimeType = array_pop($mimeType);
            $mimeType = explode(';', $mimeType);
            $mimeType = trim(array_shift($mimeType));

            if ('text/plain' !== strtolower($mimeType)) {
                return $mimeType;
            }
        }

        return null;
    }

    /**
     * @param HttpUriInterface $uri
     *
     * @return string
     */
    protected function pathFromUri(HttpUriInterface $uri)
    {
        $path = $uri->getPath();
        if (null === $path) {
            $path = '';
        }

        return $path;
    }

    private $extensionMap;
    private $browser;
}
