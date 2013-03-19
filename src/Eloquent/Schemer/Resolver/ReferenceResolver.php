<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Resolver;

use Eloquent\Schemer\Loader\Loader;
use Eloquent\Schemer\Loader\LoaderInterface;
use Eloquent\Schemer\Value\ReferenceValue;

class ReferenceResolver extends AbstractReferenceResolver
{
    /**
     * @param LoaderInterface|null $loader
     */
    public function __construct(LoaderInterface $loader = null)
    {
        if (null === $loader) {
            $loader = new Loader;
        }

        $this->loader = $loader;
    }

    /**
     * @return Loader
     */
    public function loader()
    {
        return $this->loader;
    }

    /**
     * @param ReferenceValue $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function visitReferenceValue(ReferenceValue $value)
    {
        $reference = $value->reference();
        if (!$reference->isAbsolute()) {
            // TODO
        }
    }

    private $loader;
}
