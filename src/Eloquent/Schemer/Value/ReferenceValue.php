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

use Eloquent\Schemer\Pointer\PointerInterface;
use stdClass;
use Zend\Uri\UriInterface;

class ReferenceValue implements ValueInterface
{
    /**
     * @param UriInterface          $reference
     * @param PointerInterface|null $pointer
     * @param stdClass|null         $additionalProperties
     */
    public function __construct(
        UriInterface $reference,
        PointerInterface $pointer = null,
        stdClass $additionalProperties = null
    ) {
        if (null === $additionalProperties) {
            $additionalProperties = new stdClass;
        }

        $this->reference = $reference;
        $this->pointer = $pointer;
        $this->additionalProperties = $additionalProperties;
    }

    /**
     * @return UriInterface
     */
    public function reference()
    {
        return $this->reference;
    }

    /**
     * @return PointerInterface|null
     */
    public function pointer()
    {
        return $this->pointer;
    }

    /**
     * @return stdClass
     */
    public function additionalProperties()
    {
        return $this->additionalProperties;
    }

    /**
     * @return Uri
     */
    public function uri()
    {
        $uri = new Uri($this->reference()->toString());
        $uri->setFragment($this->pointer()->string());

        return $uri;
    }

    /**
     * @param ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ValueVisitorInterface $visitor)
    {
        return $visitor->visitReferenceValue($this);
    }

    private $reference;
    private $pointer;
    private $additionalProperties;
}
