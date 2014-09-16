<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri\Resolution;

use Eloquent\Schemer\Uri\Exception\InvalidUriException;
use Eloquent\Schemer\Uri\Resolution\Exception\UriResolutionException;
use Exception;
use GuzzleHttp\Url;

/**
 * Resolves URI references against a base URI.
 */
class UriResolver implements UriResolverInterface
{
    /**
     * Get a static URI resolver instance.
     *
     * @return UriResolverInterface The static URI resolver instance.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Resolve a URI reference against a base URI.
     *
     * @param string $baseUri The base URI.
     * @param string $uri     The URI reference.
     *
     * @return string                 The resolved URI.
     * @throws UriResolutionException If the URI reference cannot be resolved.
     */
    public function resolve($baseUri, $uri)
    {
        try {
            $baseUriObject = Url::fromString($baseUri);
        } catch (Exception $e) {
            throw new UriResolutionException(
                $uri,
                $baseUri,
                new InvalidUriException($baseUri, $e)
            );
        }

        try {
            $resolved = $baseUriObject->combine($uri);
        } catch (Exception $e) {
            throw new UriResolutionException(
                $uri,
                $baseUri,
                new InvalidUriException($uri, $e)
            );
        }

        return strval($resolved);
    }

    private static $instance;
}
