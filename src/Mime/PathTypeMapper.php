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
        switch (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            case 'json':
                return 'application/json';

            case 'toml':
                return 'text/x-toml';

            case 'yaml':
            case 'yml':
                return 'application/x-yaml';
        }

        return null;
    }

    private static $instance;
}
