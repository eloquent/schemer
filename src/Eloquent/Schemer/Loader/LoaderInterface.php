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

use Zend\Uri\UriInterface;

interface LoaderInterface
{
    /**
     * @param string $defaultType
     */
    public function setDefaultType($defaultType);

    /**
     * @return string
     */
    public function defaultType();

    /**
     * @param UriInterface $uri
     *
     * @return Content
     * @throws Exception\LoadExceptionInterface
     */
    public function load(UriInterface $uri);
}
