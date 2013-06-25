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
use Eloquent\Schemer\Loader\Exception\InvalidUriTypeException;
use Eloquent\Schemer\Loader\LoaderInterface;
use Eloquent\Schemer\Uri\DataUriInterface;
use Eloquent\Schemer\Uri\UriInterface;

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
     * @param UriInterface $uri
     *
     * @return Content
     */
    public function load(UriInterface $uri)
    {
        if (!$uri instanceof DataUriInterface) {
            throw new InvalidUriTypeException(
                $uri,
                'Eloquent\Schemer\Uri\DataUriInterface'
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
