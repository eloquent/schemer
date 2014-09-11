<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Persistence;

/**
 * Represents a packet of data and its associated MIME type.
 */
class DataPacket
{
    /**
     * Construct a new data packet.
     *
     * @param string      $data     The data.
     * @param string|null $mimeType The MIME type, or null if unknown.
     */
    public function __construct($data, $mimeType = null)
    {
        $this->data = $data;
        $this->mimeType = $mimeType;
    }

    /**
     * Get the data.
     *
     * @return string The data.
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Get the MIME type.
     *
     * @return string|null The MIME type, or null if unknown.
     */
    public function mimeType()
    {
        return $this->mimeType;
    }

    private $data;
    private $mimeType;
}
