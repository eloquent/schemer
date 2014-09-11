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

use Eloquent\Schemer\Serialization\Exception\JsonException;
use Eloquent\Schemer\Serialization\Exception\SerializeException;
use Eloquent\Schemer\Serialization\Exception\UnserializeException;
use Icecave\Isolator\Isolator;

/**
 * Serializes and unserializes JSON data.
 */
class JsonDataSerializer implements DataSerializerInterface
{
    /**
     * Get a static JSON data serializer instance.
     *
     * @return DataSerializerInterface The static JSON data serializer instance.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Construct a new JSON data serializer.
     *
     * @param integer       $serializeOptions The options to pass to json_encode().
     * @param Isolator|null $isolator         The isolator to use.
     */
    public function __construct(
        $serializeOptions = null,
        Isolator $isolator = null
    ) {
        if (null === $serializeOptions) {
            $serializeOptions =  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE;
        }

        $this->serializeOptions = $serializeOptions;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * Get the serialize options.
     *
     * @return integer The options passed to json_encode().
     */
    public function serializeOptions()
    {
        return $this->serializeOptions;
    }

    /**
     * Serialize a value.
     *
     * @param mixed $value The value to serialize.
     *
     * @return string             The serialized data.
     * @throws SerializeException If the value cannot be serialized.
     */
    public function serialize($value)
    {
        $data = @json_encode($value, $this->serializeOptions());

        $error = $this->isolator()->json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            throw new SerializeException($value, new JsonException($error));
        }

        return $data;
    }

    /**
     * Unserialize some data.
     *
     * @param string $data The data to unserialize.
     *
     * @return mixed                The unserialized value.
     * @throws UnserializeException If the data cannot be unserialized.
     */
    public function unserialize($data)
    {
        $value = @json_decode($data);

        $error = $this->isolator()->json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            throw new UnserializeException($data, new JsonException($error));
        }

        return $value;
    }

    /**
     * Get the isolator.
     *
     * @return Isolator The isolator.
     */
    protected function isolator()
    {
        return $this->isolator;
    }

    private static $instance;
    private $serializeOptions;
    private $isolator;
}
