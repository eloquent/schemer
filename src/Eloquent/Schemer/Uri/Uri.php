<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri;

use Zend\Uri\Exception\InvalidUriException;
use Zend\Uri\Uri as ZendUri;

class Uri extends ZendUri
{
    /**
     * @param string $uri
     *
     * @return Uri
     */
    public function parse($uri)
    {
        // Capture scheme
        if (($scheme = self::parseScheme($uri)) !== null) {
            $this->setScheme($scheme);
            $uri = substr($uri, strlen($scheme) + 1);
        }

        // Capture authority part
        if (preg_match('|^//([^/\?#]*)|', $uri, $match)) {
            $authority = $match[1];
            $uri       = substr($uri, strlen($match[0]));

            // Split authority into userInfo and host
            if (strpos($authority, '@') !== false) {
                // The userInfo can also contain '@' symbols; split $authority
                // into segments, and set it to the last segment.
                $segments  = explode('@', $authority);
                $authority = array_pop($segments);
                $userInfo  = implode('@', $segments);
                unset($segments);
                $this->setUserInfo($userInfo);
            }

            $nMatches = preg_match('/:[\d]{1,5}$/', $authority, $matches);
            if ($nMatches === 1) {
                $portLength = strlen($matches[0]);
                $port = substr($matches[0], 1);

                $this->setPort((int) $port);
                $authority = substr($authority, 0, -$portLength);
            }

            $this->setHost($authority);
        }

        if (!$uri) {
            return $this;
        }

        // Capture the path
        if (preg_match('|^[^\?#]*|', $uri, $match)) {
            $this->setPath($match[0]);
            $uri = substr($uri, strlen($match[0]));
        }

        if (!$uri) {
            return $this;
        }

        // Capture the query
        if (preg_match('|^\?([^#]*)|', $uri, $match)) {
            $this->setQuery($match[1]);
            $uri = substr($uri, strlen($match[0]));
        }
        if (!$uri) {
            return $this;
        }

        // All that's left is the fragment
        if ($uri && substr($uri, 0, 1) == '#') {
            if ('#' === $uri) {
                $this->setFragment('');
            } else {
                $this->setFragment(substr($uri, 1));
            }
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        if ($this->host) {
            if (strlen($this->path) > 0 && substr($this->path, 0, 1) != '/') {
                return false;
            }

            return true;
        }

        if ($this->userInfo || $this->port) {
            return false;
        }

        if ($this->path) {
            if (substr($this->path, 0, 2) == '//') {
                return false;
            }

            return true;
        }

        if ($this->query || null !== $this->fragment) {
            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isValidRelative()
    {
        if ($this->scheme || $this->host || $this->userInfo || $this->port) {
            return false;
        }

        if ($this->path) {
            if (substr($this->path, 0, 2) == '//') {
                return false;
            }

            return true;
        }

        if ($this->query || null !== $this->fragment) {
            return true;
        }

        return false;
    }

    /**
     * @param string $input
     *
     * @return boolean
     */
    public static function validateQueryFragment($input)
    {
        if ('' === $input) {
            return true;
        }

        return parent::validateQueryFragment($input);
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->isValid()) {
            if ($this->isAbsolute() || !$this->isValidRelative()) {
                throw new InvalidUriException(
                    'URI is not valid and cannot be converted into a string'
                );
            }
        }

        $uri = '';

        if ($this->scheme) {
            $uri .= $this->scheme . ':';
        }

        if ($this->host !== null) {
            $uri .= '//';
            if ($this->userInfo) {
                $uri .= $this->userInfo . '@';
            }
            $uri .= $this->host;
            if ($this->port) {
                $uri .= ':' . $this->port;
            }
        }

        if ($this->path) {
            $uri .= self::encodePath($this->path);
        } elseif ($this->host && ($this->query || null !== $this->fragment)) {
            $uri .= '/';
        }

        if ($this->query) {
            $uri .= "?" . self::encodeQueryFragment($this->query);
        }

        if (null !== $this->fragment) {
            $uri .= "#" . self::encodeQueryFragment($this->fragment);
        }

        return $uri;
    }

    /**
     * @return Uri
     */
    public function normalize()
    {
        parent::normalize();

        $this->fragment = static::normalizeFragment($this->fragment);

        if (
            !$this->isAbsolute() &&
            !$this->path &&
            !$this->query &&
            null === $this->fragment
        ) {
            $this->fragment = '';
        }

        return $this;
    }

    /**
     * @param string|null $fragment
     *
     * @return string|null
     */
    protected static function normalizeFragment($fragment)
    {
        if (null === $fragment || '' === $fragment) {
            return null;
        }

        return parent::normalizeFragment($fragment);
    }
}
