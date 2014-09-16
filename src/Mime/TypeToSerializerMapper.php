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
use Eloquent\Schemer\Serialization\Json\JsonDataSerializer;

/**
 * Maps MIME types to serializers.
 */
class TypeToSerializerMapper implements TypeToSerializerMapperInterface
{
    /**
     * Get a static type to serializer mapper instance.
     *
     * @return TypeToSerializerMapperInterface The static type to serializer mapper instance.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Get the default type map used by this mapper.
     *
     * @return array<string,DataSerializerInterface> The default type map.
     */
    public static function defaultTypeMap()
    {
        return [
            'application/json' => JsonDataSerializer::instance(),
        ];
    }

    /**
     * Construct a new type to serializer mapper.
     *
     * @param array<string,DataSerializerInterface>|null $typeMap The type map.
     */
    public function __construct(array $typeMap = null)
    {
        if (null === $typeMap) {
            $typeMap = static::defaultTypeMap();
        }

        $this->typeMap = $typeMap;
    }

    /**
     * Set the type map.
     *
     * @param array<string,DataSerializerInterface> $map The type map.
     */
    public function setTypeMap(array $typeMap)
    {
        $this->typeMap = $typeMap;
    }

    /**
     * Set an entry in the type map.
     *
     * @param string                  $type       The MIME type to map.
     * @param DataSerializerInterface $serializer The serializer to map to.
     */
    public function setTypeMapEntry($type, $serializer)
    {
        $this->typeMap[$type] = $serializer;
    }

    /**
     * Remove an entry from the type map.
     *
     * @param string $type The MIME type to un-map.
     *
     * @return boolean True if the type was previously mapped.
     */
    public function removeTypeMapEntry($type)
    {
        if (array_key_exists($type, $this->typeMap)) {
            unset($this->typeMap[$type]);

            return true;
        }

        return false;
    }

    /**
     * Get the type map.
     *
     * @return array<string,DataSerializerInterface> The type map.
     */
    public function typeMap()
    {
        return $this->typeMap;
    }

    /**
     * Get the appropriate serializer for the supplied MIME type.
     *
     * @param string $type The MIME type.
     *
     * @return DataSerializerInterface      The serializer.
     * @throws UnsupportedMimeTypeException If the MIME type is not supported.
     */
    public function serializerByType($type)
    {
        if (array_key_exists($type, $this->typeMap)) {
            return $this->typeMap[$type];
        }

        throw new UnsupportedMimeTypeException($type);
    }

    private static $instance;
    private $typeMap;
}
