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
 * Resolves URI references against a bound base URI.
 */
class BoundUriResolver implements BoundUriResolverInterface
{
    /**
     * Create a new bound URI resolver from a base URI string.
     *
     * @param string $baseUri The base URI.
     *
     * @return BoundUriResolverInterface The newly created bound URI resolver.
     * @throws InvalidUriException       If the supplied base URI is invalid.
     */
    public static function fromUriString($baseUri)
    {
        try {
            $baseUriObject = Url::fromString($baseUri);
        } catch (Exception $e) {
            throw new InvalidUriException($baseUri, $e);
        }

        return new static($baseUri, $baseUriObject);
    }

    /**
     * Construct a new bound URI resolver.
     *
     * @param string $baseUri The base URI.
     * @param Url    $baseUri The base URI object.
     *
     * @internal
     */
    protected function __construct($baseUri, $baseUriObject)
    {
        $this->baseUri = $baseUri;
        $this->baseUriObject = $baseUriObject;
    }

    /**
     * Get the base URI.
     *
     * @return string The base URI.
     */
    public function baseUri()
    {
        return $this->baseUri;
    }

    /**
     * Resolve a URI reference.
     *
     * @param string $uri The URI reference.
     *
     * @return string                 The resolved URI.
     * @throws UriResolutionException If the URI reference cannot be resolved.
     */
    public function resolve($uri)
    {
        try {
            $resolved = $this->baseUriObject->combine($uri);
        } catch (Exception $e) {
            throw new UriResolutionException(
                $uri,
                $this->baseUri,
                new InvalidUriException($uri, $e)
            );
        }

        return strval($resolved);
    }

    private $baseUri;
    private $baseUriObject;
}
