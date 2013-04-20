<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value;

use Zend\Uri\UriInterface;

class ReferenceValue implements ValueInterface
{
    /**
     * @param UriInterface $uri
     * @param string|null  $mimeType
     */
    public function __construct(UriInterface $uri, $mimeType = null)
    {
        $this->uri = clone $uri;
        $this->uri->normalize();
        $this->mimeType = $mimeType;
    }

    /**
     * @return UriInterface
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * @return string|null
     */
    public function mimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param Visitor\ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(Visitor\ValueVisitorInterface $visitor)
    {
        return $visitor->visitReferenceValue($this);
    }

    private $uri;
    private $mimeType;
}
