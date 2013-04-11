<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization;

use Eloquent\Schemer\Loader\ContentType;

class ProtocolMap
{
    /**
     * @param array<string,SerializationProtocolInterface>|null $map
     */
    public function __construct(array $map = null)
    {
        if (null === $map) {
            $map = array(
                ContentType::JSON()->primaryMimeType() => new Json\JsonProtocol,
                ContentType::TOML()->primaryMimeType() => new Toml\TomlProtocol,
                ContentType::YAML()->primaryMimeType() => new Yaml\YamlProtocol,
            );
        }

        $this->map = $map;
    }

    /**
     * @return array<string,SerializationProtocolInterface>
     */
    public function map()
    {
        return $this->map;
    }

    /**
     * @param string                         $mimeType
     * @param SerializationProtocolInterface $protocol
     */
    public function set($mimeType, SerializationProtocolInterface $protocol)
    {
        $this->map[$mimeType] = $protocol;
    }

    /**
     * @param string $mimeType
     *
     * @return SerializationProtocolInterface
     * @throws Exception\UndefinedProtocolException
     */
    public function get($mimeType)
    {
        if (!array_key_exists($mimeType, $this->map)) {
            throw new Exception\UndefinedProtocolException($mimeType);
        }

        return $this->map[$mimeType];
    }

    private $map;
}
