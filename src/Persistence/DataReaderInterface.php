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

use Eloquent\Schemer\Persistence\Exception\ReadException;

/**
 * The interface implemented by data readers.
 */
interface DataReaderInterface
{
    /**
     * Read data from a URI.
     *
     * @param string      $uri      The URI.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return DataPacket    The data.
     * @throws ReadException If the data cannot be read.
     */
    public function readFromUri($uri, $mimeType = null);

    /**
     * Read data from a HTTP URI.
     *
     * @param string      $uri      The URI.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return DataPacket    The data.
     * @throws ReadException If the data cannot be read.
     */
    public function readFromHttp($uri, $mimeType = null);

    /**
     * Read data from a file.
     *
     * @param string      $path     The path.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return DataPacket    The data.
     * @throws ReadException If the data cannot be read.
     */
    public function readFromFile($path, $mimeType = null);

    /**
     * Read data from a stream.
     *
     * @param stream      $stream   The stream.
     * @param string|null $mimeType The MIME type, if known.
     * @param string|null $uri      The URI, if known.
     *
     * @return DataPacket    The data.
     * @throws ReadException If the data cannot be read.
     */
    public function readFromStream($stream, $mimeType = null, $uri = null);
}
