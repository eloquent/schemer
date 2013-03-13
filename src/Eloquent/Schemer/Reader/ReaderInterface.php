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

use Zend\Uri\UriInterface;

interface ReaderInterface
{
    /**
     * @param string            $data
     * @param UriInterface|null $context
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function read($data, UriInterface $context = null);

    /**
     * @param UriInterface $uri
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readUri(UriInterface $uri);

    /**
     * @param string $path
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readPath($path);
}
