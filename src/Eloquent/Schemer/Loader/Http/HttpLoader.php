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
     * @param Browser|null $browser
     */
    public function __construct(Browser $browser = null)
    {
        if (null === $browser) {
            $browser = new Browser;
        }

        $this->browser = $browser;
        $this->defaultType = ContentType::JSON()->primaryType();
    }

    /**
     * @return Browser
     */
    public function browser()
    {
        return $this->browser;
    }

    /**
     * @param string $defaultType
     */
    public function setDefaultType($defaultType)
    {
        $this->defaultType = $defaultType;
    }

    /**
     * @return string
     */
    public function defaultType()
    {
        return $this->defaultType;
    }

    /**
     * @param UriInterface $uri
     *
     * @return Content
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
            $this->typeByResponse($response)
        );
    }

    /**
     * @param Response $response
     *
     * @return string
     */
    protected function typeByResponse(Response $response)
    {
        $type = $response->getHeader('Content-Type');
        if (count($type) > 0) {
            $type = array_pop($type);
            $type = explode(';', $type);
            $type = trim(array_shift($type));

            return $type;
        }

        return $this->defaultType();
    }

    private $browser;
    private $defaultType;
}
