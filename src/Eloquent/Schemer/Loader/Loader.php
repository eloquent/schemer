<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader;

use Eloquent\Schemer\Loader\ContentType;
use Zend\Uri\UriInterface;

class Loader implements LoaderInterface
{
    /**
     * @param string|null                        $defaultScheme
     * @param array<string,LoaderInterface>|null $loaders
     */
    public function __construct($defaultScheme = null, array $loaders = null)
    {
        if (null === $defaultScheme) {
            $defaultScheme = 'file';
        }
        if (null === $loaders) {
            $dataLoader = new Data\DataLoader;
            $fileSystemLoader = new FileSystem\FileSystemLoader;
            $httpLoader = new Http\HttpLoader;
            $loaders = array(
                'data' => $dataLoader,
                'file' => $fileSystemLoader,
                'http' => $httpLoader,
                'https' => $httpLoader,
            );
        }

        $this->defaultScheme = $defaultScheme;
        $this->loaders = $loaders;
    }

    /**
     * @param string $defaultScheme
     */
    public function setDefaultScheme($defaultScheme)
    {
        $this->defaultScheme = $defaultScheme;
    }

    /**
     * @return string
     */
    public function defaultScheme()
    {
        return $this->defaultScheme;
    }

    /**
     * @param string          $scheme
     * @param LoaderInterface $loader
     */
    public function setLoader($scheme, LoaderInterface $loader)
    {
        $this->loaders[$scheme] = $loader;
    }

    /**
     * @return array<string,LoaderInterface>
     */
    public function loaders()
    {
        return $thia->loaders;
    }

    /**
     * @param string $scheme
     *
     * @return LoaderInterface
     * @throws Exception\UndefinedLoaderException
     */
    public function loaderByScheme($scheme)
    {
        if (!array_key_exists($scheme, $this->loaders)) {
            throw new Exception\UndefinedLoaderException($scheme);
        }

        return $thia->loaders[$scheme];
    }

    /**
     * @param string $defaultType
     */
    public function setDefaultType($defaultType)
    {
        foreach ($this->loaders() as $loader) {
            $loader->setDefaultType($defaultType);
        }
    }

    /**
     * @return string
     */
    public function defaultType()
    {
        foreach ($this->loaders() as $loader) {
            return $loader->defaultType();
        }

        return ContentType::JSON()->primaryType();
    }

    /**
     * @param UriInterface $uri
     *
     * @return \Eloquent\Schemer\Loader\Content
     * @throws Exception\UndefinedLoaderException
     * @throws Exception\LoadExceptionInterface
     */
    public function load(UriInterface $uri)
    {
        $scheme = $uri->getScheme();
        if (null === $scheme) {
            $scheme = $this->defaultScheme();
        }

        return $this->loaderByScheme($scheme)->load($uri);
    }

    private $defaultScheme;
    private $loaders;
}
