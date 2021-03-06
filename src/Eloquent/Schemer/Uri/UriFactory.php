<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri;

use Icecave\Isolator\Isolator;
use InvalidArgumentException;

class UriFactory implements UriFactoryInterface
{
    /**
     * @param array<string,string>|null $schemeClasses
     * @param string|null               $defaultClass
     * @param Isolator|null             $isolator
     */
    public function __construct(
        array $schemeClasses = null,
        $defaultClass = null,
        Isolator $isolator = null
    ) {
        if (null === $schemeClasses) {
            $schemeClasses = array(
                'data' => __NAMESPACE__ . '\DataUri',
                'file' => __NAMESPACE__ . '\FileUri',
                'http' => __NAMESPACE__ . '\HttpUri',
                'https' => __NAMESPACE__ . '\HttpUri',
            );
        }
        if (null === $defaultClass) {
            $defaultClass = __NAMESPACE__ . '\Uri';
        }

        $this->schemeClasses = $schemeClasses;
        $this->defaultClass = $defaultClass;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @param string $scheme
     * @param string $class
     */
    public function setSchemeClass($scheme, $class)
    {
        $this->schemeClasses[$scheme] = $class;
    }

    /**
     * @return array<string,string>
     */
    public function schemeClasses()
    {
        return $this->schemeClasses;
    }

    /**
     * @param string|null $scheme
     *
     * @return string
     */
    public function schemeClass($scheme)
    {
        if (null === $scheme) {
            return $this->defaultClass();
        }

        $scheme = strtolower($scheme);
        $schemeClasses = $this->schemeClasses();
        if (!array_key_exists($scheme, $schemeClasses)) {
            return $this->defaultClass();
        }

        return $schemeClasses[$scheme];
    }

    /**
     * @return string
     */
    public function defaultClass()
    {
        return $this->defaultClass;
    }

    /**
     * @param string      $uri
     * @param string|null $defaultScheme
     *
     * @return UriInterface
     */
    public function create($uri, $defaultScheme = null)
    {
        if (!is_string($uri)) {
            throw new InvalidArgumentException('URI must be a string.');
        }

        $scheme = Uri::parseScheme($uri);
        if (null === $scheme) {
            $scheme = $defaultScheme;
        }
        $schemeClass = $this->schemeClass($scheme);

        return new $schemeClass($uri);
    }

    /**
     * @param string $uri
     *
     * @return UriInterface
     */
    public function createGeneric($uri)
    {
        if (!is_string($uri)) {
            throw new InvalidArgumentException('URI must be a string.');
        }

        $schemeClass = $this->defaultClass();

        return new $schemeClass($uri);
    }

    /**
     * @param string $path
     *
     * @return FileUri
     */
    public function fromPath($path)
    {
        $schemeClass = $this->schemeClass('file');
        if ($this->isolator->defined('PHP_WINDOWS_VERSION_BUILD')) {
            $uri = $schemeClass::fromWindowsPath($path);
        } else {
            $uri = $schemeClass::fromUnixPath($path);
        }

        return $uri;
    }

    /**
     * @param string      $data
     * @param string|null $mimeType
     *
     * @return DataUri
     */
    public function fromData($data, $mimeType = null)
    {
        $schemeClass = $this->schemeClass('data');
        $uri = new $schemeClass;
        $uri->setData($data);
        if (null !== $mimeType) {
            $uri->setMimeType($mimeType);
        }

        return $uri;
    }

    private $schemeClasses;
    private $defaultClass;
    private $isolator;
}
