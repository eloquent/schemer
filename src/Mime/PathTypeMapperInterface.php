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
 * The interface implemented by path type mappers.
 */
interface PathTypeMapperInterface
{
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
    public function typeByPath($path);
}
