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

use InvalidArgumentException;
use Zend\Uri\Uri;

class UriFactory implements UriFactoryInterface
{
    /**
     * @param array<string,string>|null $schemeClasses
     * @param string|null               $defaultClass
     */
    public function __construct(
        array $schemeClasses = null,
        $defaultClass = null
    ) {
        if (null === $schemeClasses) {
            $schemeClasses = array(
                'data' => __NAMESPACE__ . '\DataUri',
                'file' => 'Zend\Uri\File',
                'http' => 'Zend\Uri\Http',
                'https' => 'Zend\Uri\Http',
                'mailto' => 'Zend\Uri\Mailto',
                'tag' => 'Zend\Uri\Uri',
                'urn' => 'Zend\Uri\Uri',
            );
        }
        if (null === $defaultClass) {
            $defaultClass = 'Zend\Uri\Uri';
        }

        $this->schemeClasses = $schemeClasses;
        $this->defaultClass = $defaultClass;
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
     * @return \Zend\Uri\UriInterface
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

    private $schemeClasses;
    private $defaultClass;
}
