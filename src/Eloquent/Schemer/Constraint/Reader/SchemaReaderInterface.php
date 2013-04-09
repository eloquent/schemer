<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Reader;

interface SchemaReaderInterface
{
    /**
     * @param \Zend\Uri\UriInterface|string $uri
     * @param string|null                   $type
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function read($uri, $type = null);

    /**
     * @param string      $path
     * @param string|null $type
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function readPath($path, $type = null);

    /**
     * @param string      $data
     * @param string|null $type
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function readString($data, $type = null);
}
