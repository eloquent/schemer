<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Constraint;

use Eloquent\Schemer\Constraint\SchemaInterface;
use Eloquent\Schemer\Serialization\Exception\SerializeException;
use Eloquent\Schemer\Serialization\Exception\UnserializeException;

/**
 * The interface implemented by schema serializers.
 */
interface SchemaSerializerInterface
{
    /**
     * Serialize a schema.
     *
     * @param SchemaInterface $schema The schema to serialize.
     *
     * @return string             The serialized schema.
     * @throws SerializeException If the schema cannot be serialized.
     */
    public function serialize($schema);

    /**
     * Unserialize a schema.
     *
     * @param string $data The schema data to unserialize.
     *
     * @return SchemaInterface      The unserialized schema.
     * @throws UnserializeException If the schema cannot be unserialized.
     */
    public function unserialize($data);
}
