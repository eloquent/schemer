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
use Eloquent\Schemer\Loader\Exception\LoadException;
use Eloquent\Schemer\Loader\LoaderInterface;
use ErrorException;
use Icecave\Isolator\Isolator;
use InvalidArgumentException;
use Zend\Uri\File as FileUri;
use Zend\Uri\UriInterface;

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
     * @param string $defaultType
     */
    public function setDefaultType($defaultType)
    {
        $this->extensionMap()->setDefault($defaultType);
    }

    /**
     * @return string
     */
    public function defaultType()
    {
        return $this->extensionMap()->default();
    }

    /**
     * @param UriInterface $uri
     *
     * @return Content
     * @throws LoadException
     */
    public function load(UriInterface $uri)
    {
        if (!$uri instanceof FileUri) {
            throw new InvalidArgumentException('URI must be a file URI.');
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
    protected function pathFromUri(FileUri $uri)
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
