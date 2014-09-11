<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization;

use Eloquent\Schemer\Serialization\Exception\SerializeException;
use Eloquent\Schemer\Serialization\Exception\UnserializeException;

/**
 * The interface implemented by data serializers.
 */
interface DataSerializerInterface
{
    /**
     * Serialize a value.
     *
     * @param mixed $value The value to serialize.
     *
     * @return string             The serialized data.
     * @throws SerializeException If the value cannot be serialized.
     */
    public function serialize($value);

    /**
     * Unserialize some data.
     *
     * @param string $data The data to unserialize.
     *
     * @return mixed                The unserialized value.
     * @throws UnserializeException If the data cannot be unserialized.
     */
    public function unserialize($data);
}
