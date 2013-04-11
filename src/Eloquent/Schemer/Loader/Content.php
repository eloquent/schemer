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

class Content
{
    /**
     * @param string $data
     * @param string $mimeType
     */
    public function __construct($data, $mimeType)
    {
        $this->data = $data;
        $this->mimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function mimeType()
    {
        return $this->mimeType;
    }

    private $data;
    private $mimeType;
}
