<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Mime;

use Eloquent\Schemer\Mime\Exception\UnsupportedMimeTypeException;
use Eloquent\Schemer\Serialization\DataSerializerInterface;

/**
 * The interface implemented by type to serializer mappers.
 */
interface TypeToSerializerMapperInterface
{
    /**
     * Get the appropriate serializer for the supplied MIME type.
     *
     * @param string $type The MIME type.
     *
     * @return DataSerializerInterface      The serializer.
     * @throws UnsupportedMimeTypeException If the MIME type is not supported.
     */
    public function serializerByType($type);
}
