<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader\FileSystem;

use Eloquent\Schemer\Loader\Content;
use Eloquent\Schemer\Loader\Exception\InvalidUriTypeException;
use Eloquent\Schemer\Loader\Exception\LoadException;
use Eloquent\Schemer\Loader\ExtensionTypeMap;
use Eloquent\Schemer\Loader\LoaderInterface;
use Eloquent\Schemer\Uri\FileUriInterface;
use Eloquent\Schemer\Uri\UriInterface;
use ErrorException;
use Icecave\Isolator\Isolator;

class FileSystemLoader implements LoaderInterface
{
    /**
     * @param ExtensionTypeMap|null $extensionMap
     * @param Isolator|null         $isolator
     */
    public function __construct(
        ExtensionTypeMap $extensionMap = null,
        Isolator $isolator = null
    ) {
        if (null === $extensionMap) {
            $extensionMap = new ExtensionTypeMap;
        }

        $this->extensionMap = $extensionMap;
        $this->isolator = Isolator::get($isolator);
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
     * @param UriInterface $uri
     *
     * @return Content
     * @throws LoadException
     */
    public function load(UriInterface $uri)
    {
        if (!$uri instanceof FileUriInterface) {
            throw new InvalidUriTypeException(
                $uri,
                'Eloquent\Schemer\Uri\FileUriInterface'
            );
        }

        $path = $this->pathFromUri($uri);

        try {
            $data = $this->isolator->file_get_contents($path);
        } catch (ErrorException $e) {
            throw new LoadException($uri, $e);
        }

        return new Content($data, $this->extensionMap()->getByPath($path));
    }

    /**
     * @param FileUri $uri
     *
     * @return string
     */
    protected function pathFromUri(FileUriInterface $uri)
    {
        $path = $uri->getPath();
        if (null === $path) {
            $path = '';
        }

        return $path;
    }

    private $extensionMap;
    private $isolator;
}
