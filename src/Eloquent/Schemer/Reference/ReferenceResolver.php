<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference;

use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Reader\ReaderInterface;
use Eloquent\Schemer\Uri\Resolver\BoundUriResolverInterface;
use Eloquent\Schemer\Value\ReferenceValue;

class ReferenceResolver extends AbstractReferenceResolver
{
    /**
     * @param BoundUriResolverInterface $uriResolver
     * @param ReaderInterface|null      $reader
     */
    public function __construct(
        BoundUriResolverInterface $uriResolver,
        ReaderInterface $reader = null
    ) {
        if (null === $reader) {
            $reader = new Reader;
        }

        $this->uriResolver = $uriResolver;
        $this->reader = $reader;
    }

    /**
     * @return BoundUriResolverInterface
     */
    public function uriResolver()
    {
        return $this->uriResolver;
    }

    /**
     * @return Reader
     */
    public function reader()
    {
        return $this->reader;
    }

    /**
     * @param ReferenceValue $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function visitReferenceValue(ReferenceValue $value)
    {
        $uri = $value->reference();
        if (!$uri->isAbsolute()) {
            $uri = $this->uriResolver()->resolve($uri);
        }

        return $this->reader()->read($uri);
    }

    private $uriResolver;
    private $reader;
}
