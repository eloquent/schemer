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
                ContentType::JSON()->primaryType() => new Json\JsonProtocol,
                ContentType::TOML()->primaryType() => new Toml\TomlProtocol,
                ContentType::YAML()->primaryType() => new Yaml\YamlProtocol,
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
     * @param string                         $type
     * @param SerializationProtocolInterface $protocol
     */
    public function set($type, SerializationProtocolInterface $protocol)
    {
        $this->map[$type] = $protocol;
    }

    /**
     * @param string $type
     *
     * @return SerializationProtocolInterface
     * @throws Exception\UndefinedProtocolException
     */
    public function get($type)
    {
        if (!array_key_exists($type, $this->map)) {
            throw new Exception\UndefinedProtocolException($type);
        }

        return $this->map[$type];
    }

    private $map;
}
