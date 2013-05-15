<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reader;

use Eloquent\Schemer\Reference\ReferenceResolverFactoryInterface;
use Eloquent\Schemer\Reference\SwitchingScopeReferenceResolverFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;

class SwitchingScopeResolvingReader extends AbstractResolvingReader
{
    /**
     * @param ReferenceResolverFactoryInterface|null $resolverFactory
     * @param ReaderInterface|null                   $reader
     * @param UriFactoryInterface|null               $uriFactory
     */
    public function __construct(
        ReferenceResolverFactoryInterface $resolverFactory = null,
        ReaderInterface $reader = null,
        UriFactoryInterface $uriFactory = null
    ) {
        if (null === $resolverFactory) {
            $resolverFactory = new SwitchingScopeReferenceResolverFactory;
        }

        parent::__construct($resolverFactory, $reader, $uriFactory);
    }
}
