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

/**
 * Maps file paths to MIME types.
 */
class PathTypeMapper implements PathTypeMapperInterface
{
    /**
     * Get a static path type mapper instance.
     *
     * @return PathTypeMapperInterface The static path type mapper instance.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Get the default extension map used by this mapper.
     *
     * @return array<string,string> The default extension map.
     */
    public static function defaultExtensionMap()
    {
        return [
            'json' => 'application/json',
            'toml' => 'text/x-toml',
            'yml' => 'application/x-yaml',
            'yaml' => 'application/x-yaml',
        ];
    }

    /**
     * Construct a new path type mapper.
     *
     * @param array<string,string>|null $extensionMap The extension map.
     */
    public function __construct(array $extensionMap = null)
    {
        if (null === $extensionMap) {
            $extensionMap = static::defaultExtensionMap();
        }

        $this->extensionMap = $extensionMap;
    }

    /**
     * Set the extension map.
     *
     * @param array<string,string> $map The extension map.
     */
    public function setExtensionMap(array $extensionMap)
    {
        $this->extensionMap = $extensionMap;
    }

    /**
     * Set an entry in the extension map.
     *
     * @param string $extension The extension to map.
     * @param string $type      The MIME type to map to.
     */
    public function setExtensionMapEntry($extension, $type)
    {
        $this->extensionMap[$extension] = $type;
    }

    /**
     * Remove an entry from the extension map.
     *
     * @param string $extension The extension to un-map.
     *
     * @return boolean True if the extension was previously mapped.
     */
    public function removeExtensionMapEntry($extension)
    {
        if (array_key_exists($extension, $this->extensionMap)) {
            unset($this->extensionMap[$extension]);

            return true;
        }

        return false;
    }

    /**
     * Get the extension map.
     *
     * @return array<string,string> The extension map.
     */
    public function extensionMap()
    {
        return $this->extensionMap;
    }

    /**
     * Get the most likely MIME type for the supplied path.
     *
     * This method does not involve file system access, and guesses solely upon
     * the content of the path string.
     *
     * @param string $path The path.
     *
     * @return string|null The MIME type, or null if unknown.
     */
    public function typeByPath($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (array_key_exists($extension, $this->extensionMap)) {
            return $this->extensionMap[$extension];
        }

        return null;
    }

    private static $instance;
    private $extensionMap;
}
