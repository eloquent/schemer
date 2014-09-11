<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Persistence;

use Eloquent\Schemer\Persistence\Exception\ReadException;

/**
 * The interface implemented by schema readers.
 */
interface SchemaReaderInterface
{
    /**
     * Read a schema from a URI
     *
     * @param string      $uri      The URI.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return SchemaInterface The schema.
     * @throws ReadException   If the schema cannot be read.
     */
    public function readFromUri($uri, $mimeType = null);

    /**
     * Read a schema from a file.
     *
     * @param string      $path     The path.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return SchemaInterface The schema.
     * @throws ReadException   If the schema cannot be read.
     */
    public function readFromFile($path, $mimeType = null);

    /**
     * Read a schema from a stream.
     *
     * @param stream      $stream   The stream.
     * @param string|null $mimeType The MIME type, if known.
     * @param string|null $path     The path, if known.
     *
     * @return SchemaInterface The schema.
     * @throws ReadException   If the schema cannot be read.
     */
    public function readFromStream($stream, $mimeType = null, $path = null);

    /**
     * Read a schema from a string.
     *
     * @param string      $data     The string.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return SchemaInterface The schema.
     * @throws ReadException   If the schema cannot be read.
     */
    public function readFromString($data, $mimeType = null);
}
