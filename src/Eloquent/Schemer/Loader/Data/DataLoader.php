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
     * @param string|null $defaultType
     */
    public function __construct($defaultType = null)
    {
        if (null === $defaultType) {
            $defaultType = ContentType::JSON()->primaryType();
        }

        $this->defaultType = $defaultType;
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
        if (!$uri instanceof DataUri) {
            throw new InvalidArgumentException(
                'URI must be a data URI.'
            );
        }

        return new Content(
            $uri->getData(),
            $this->stripTypeParameters($uri->getMimeType())
        );
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function stripTypeParameters($type)
    {
        $type = explode(';', $type);
        $type = trim(array_shift($type));

        return $type;
    }

    private $defaultType;
}
