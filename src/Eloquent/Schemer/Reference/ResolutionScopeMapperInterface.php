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

use Eloquent\Schemer\Value;
use Zend\Uri\UriInterface;

interface ResolutionScopeMapperInterface
{
    /**
     * @param UriInterface         $baseUri
     * @param Value\ValueInterface $value
     *
     * @return ResolutionScopeMap
     */
    public function create(UriInterface $baseUri, Value\ValueInterface $value);
}
