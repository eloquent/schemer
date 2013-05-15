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
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;

abstract class AbstractReader implements ReaderInterface
{
    /**
     * @param UriFactoryInterface|null $uriFactory
     */
    public function __construct(UriFactoryInterface $uriFactory = null)
    {
        if (null === $uriFactory) {
            $uriFactory = new UriFactory;
        }

        $this->uriFactory = $uriFactory;
    }

    /**
     * @return UriFactoryInterface
     */
    public function uriFactory()
    {
        return $this->uriFactory;
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

    private $uriFactory;
}
