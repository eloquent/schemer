<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri\Resolver;

use Eloquent\Schemer\Uri\UriInterface;

class BoundUriResolver implements BoundUriResolverInterface
{
    /**
     * @param UriInterface              $baseUri
     * @param UriResolverInterface|null $resolver
     */
    public function __construct(
        UriInterface $baseUri,
        UriResolverInterface $resolver = null
    ) {
        if (null === $resolver) {
            $resolver = new UriResolver;
        }

        $this->baseUri = $baseUri;
        $this->resolver = $resolver;
    }

    /**
     * @return UriInterface
     */
    public function baseUri()
    {
        return $this->baseUri;
    }

    /**
     * @return UriResolverInterface
     */
    public function resolver()
    {
        return $this->resolver;
    }

    /**
     * @param UriInterface $uri
     *
     * @return UriInterface
     */
    public function resolve(UriInterface $uri)
    {
        return $this->resolver()->resolve($uri, $this->baseUri());
    }

    private $baseUri;
    private $resolver;
}
