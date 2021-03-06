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

interface UriFactoryInterface
{
    /**
     * @param string      $uri
     * @param string|null $defaultScheme
     *
     * @return UriInterface
     */
    public function create($uri, $defaultScheme = null);

    /**
     * @param string $uri
     *
     * @return UriInterface
     */
    public function createGeneric($uri);

    /**
     * @param string $path
     *
     * @return FileUri
     */
    public function fromPath($path);

    /**
     * @param string      $data
     * @param string|null $mimeType
     *
     * @return DataUri
     */
    public function fromData($data, $mimeType = null);
}
