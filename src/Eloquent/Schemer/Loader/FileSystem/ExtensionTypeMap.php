<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader\FileSystem;

use Eloquent\Schemer\Loader\ContentType;

class ExtensionTypeMap
{
    /**
     * @param array<string,string>|null $map
     * @param string|null               $default
     */
    public function __construct(array $map = null, $default = null)
    {
        if (null === $map) {
            $jsonType = ContentType::JSON()->primaryType();
            $tomlType = ContentType::TOML()->primaryType();
            $yamlType = ContentType::YAML()->primaryType();
            $map = array(
                'js' => $jsonType,
                'json' => $jsonType,
                'toml' => $tomlType,
                'tml' => $tomlType,
                'yaml' => $yamlType,
                'yml' => $yamlType,
            );
        }
        if (null === $default) {
            $default = ContentType::JSON()->primaryType();
        }

        $this->map = $map;
        $this->default = $default;
    }

    /**
     * @return array<string,string>
     */
    public function map()
    {
        return $this->map;
    }

    /**
     * @param string $extension
     * @param string $type
     */
    public function set($extension, $type)
    {
        $this->map[$extension] = $type;
    }

    /**
     * @param string $type
     */
    public function setDefault($type)
    {
        $this->default = $type;
    }

    /**
     * @param string|null $extension
     *
     * @return string
     */
    public function get($extension)
    {
        if (null !== $extension && array_key_exists($extension, $this->map)) {
            return $this->map[$extension];
        }

        return $this->default();
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getByPath($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if ('' === $extension) {
            $extension = null;
        }

        return $this->get($extension);
    }

    /**
     * @return string
     */
    public function default()
    {
        return $this->default;
    }

    private $map;
    private $default;
}
