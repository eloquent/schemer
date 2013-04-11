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
     * @param string|null               $defaultMimeType
     */
    public function __construct(array $map = null, $defaultMimeType = null)
    {
        if (null === $map) {
            $jsonType = ContentType::JSON()->primaryMimeType();
            $tomlType = ContentType::TOML()->primaryMimeType();
            $yamlType = ContentType::YAML()->primaryMimeType();
            $map = array(
                'js' => $jsonType,
                'json' => $jsonType,
                'toml' => $tomlType,
                'tml' => $tomlType,
                'yaml' => $yamlType,
                'yml' => $yamlType,
            );
        }
        if (null === $defaultMimeType) {
            $defaultMimeType = ContentType::JSON()->primaryMimeType();
        }

        $this->map = $map;
        $this->defaultMimeType = $defaultMimeType;
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
     * @param string $mimeType
     */
    public function set($extension, $mimeType)
    {
        $this->map[$extension] = $mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setDefaultMimeType($mimeType)
    {
        $this->defaultMimeType = $mimeType;
    }

    /**
     * @return string
     */
    public function defaultMimeType()
    {
        return $this->defaultMimeType;
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

        return $this->defaultMimeType();
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

    private $map;
    private $defaultMimeType;
}
