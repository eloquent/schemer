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
use Eloquent\Schemer\Loader\Exception\LoadException;
use Eloquent\Schemer\Loader\LoaderInterface;
use InvalidArgumentException;
use Zend\Uri\Http as HttpUri;
use Zend\Uri\UriInterface;

class HttpLoader implements LoaderInterface
{
    /**
     * @param string|null  $defaultMimeType
     * @param Browser|null $browser
     */
    public function __construct($defaultMimeType = null, Browser $browser = null)
    {
        if (null === $defaultMimeType) {
            $defaultMimeType = ContentType::JSON()->primaryMimeType();
        }
        if (null === $browser) {
            $browser = new Browser;
        }

        $this->defaultMimeType = $defaultMimeType;
        $this->browser = $browser;
    }

    /**
     * @param string $mimeType
     */
    public function setDefaultMimeType($mimeType)
    {
        $this->defaultMimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function defaultMimeType()
    {
        return $this->defaultMimeType;
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
        if (!$uri instanceof HttpUri || !$uri->isAbsolute()) {
            throw new InvalidArgumentException(
                'URI must be an absolute HTTP URI.'
            );
        }

        $response = $this->browser()->get($uri->toString());
        if (!$response->isSuccessful()) {
            throw new LoadException($uri);
        }

        return new Content(
            $response->getContent(),
            $this->mimeTypeByResponse($response)
        );
    }

    /**
     * @param Response $response
     *
     * @return string
     */
    protected function mimeTypeByResponse(Response $response)
    {
        $mimeType = $response->getHeader('Content-Type');
        if (count($mimeType) > 0) {
            $mimeType = array_pop($mimeType);
            $mimeType = explode(';', $mimeType);
            $mimeType = trim(array_shift($mimeType));

            return $mimeType;
        }

        return $this->defaultMimeType();
    }

    private $defaultMimeType;
    private $browser;
}
