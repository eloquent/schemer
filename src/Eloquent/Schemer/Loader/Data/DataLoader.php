<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader\Data;

use Eloquent\Schemer\Loader\Content;
use Eloquent\Schemer\Loader\ContentType;
use Eloquent\Schemer\Loader\LoaderInterface;
use Eloquent\Schemer\Uri\DataUri;
use InvalidArgumentException;
use Zend\Uri\UriInterface;

class DataLoader implements LoaderInterface
{
    /**
     * @param string|null $defaultMimeType
     */
    public function __construct($defaultMimeType = null)
    {
        if (null === $defaultMimeType) {
            $defaultMimeType = ContentType::JSON()->primaryMimeType();
        }

        $this->defaultMimeType = $defaultMimeType;
    }

    /**
     * @param string $defaultMimeType
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
     * @param UriInterface $uri
     *
     * @return Content
     */
    public function load(UriInterface $uri)
    {
        if (!$uri instanceof DataUri) {
            throw new InvalidArgumentException(
                'URI must be a data URI.'
            );
        }

        return new Content(
            $uri->getData(),
            $this->stripMimeTypeParameters($uri->getMimeType())
        );
    }

    /**
     * @param string $mimeType
     *
     * @return string
     */
    protected function stripMimeTypeParameters($mimeType)
    {
        $mimeType = explode(';', $mimeType);
        $mimeType = trim(array_shift($mimeType));

        return $mimeType;
    }

    private $defaultMimeType;
}
