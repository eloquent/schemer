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

use Eloquent\Schemer\Pointer\PointerFactory;
use Eloquent\Schemer\Pointer\PointerFactoryInterface;
use Eloquent\Schemer\Uri\UriInterface;
use Eloquent\Schemer\Value;

class FixedResolutionScopeMapFactory extends Value\Visitor\AbstractValueVisitor implements
    ResolutionScopeMapFactoryInterface
{
    /**
     * @param PointerFactoryInterface|null $pointerFactory
     */
    public function __construct(PointerFactoryInterface $pointerFactory = null)
    {
        if (null === $pointerFactory) {
            $pointerFactory = new PointerFactory;
        }

        $this->pointerFactory = $pointerFactory;
    }

    /**
     * @return PointerFactoryInterface
     */
    public function pointerFactory()
    {
        return $this->pointerFactory;
    }

    /**
     * @param UriInterface         $baseUri
     * @param Value\ValueInterface $value
     *
     * @return ResolutionScopeMap
     */
    public function create(UriInterface $baseUri, Value\ValueInterface $value)
    {
        return new ResolutionScopeMap(
            array(
                array($this->pointerFactory()->create(), $baseUri),
            ),
            $value
        );
    }

    private $pointerFactory;
}
