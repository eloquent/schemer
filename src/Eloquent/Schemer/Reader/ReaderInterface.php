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

interface ReaderInterface
{
    /**
     * @param \Zend\Uri\UriInterface|string $uri
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function read($uri);

    /**
     * @param string $path
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readPath($path);

    /**
     * @param string      $data
     * @param string|null $type
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readString($data, $type = null);
}
