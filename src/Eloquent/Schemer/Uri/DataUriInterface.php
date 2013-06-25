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

interface DataUriInterface extends UriInterface
{
    /**
     * @param string|null $mimeType
     */
    public function setMimeType($mimeType);

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param string|null $encoding
     */
    public function setEncoding($encoding);

    /**
     * @return string|null
     */
    public function getEncoding();

    /**
     * @param string $rawData
     */
    public function setRawData($rawData);

    /**
     * @return string
     */
    public function getRawData();

    /**
     * @param string $data
     */
    public function setData($data);

    /**
     * @return string
     */
    public function getData();
}
