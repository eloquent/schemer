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

use stdClass;
use Zend\Uri\UriInterface;

class ReferenceValue implements ValueInterface
{
    /**
     * @param UriInterface  $reference
     * @param stdClass|null $additionalProperties
     */
    public function __construct(UriInterface $reference, stdClass $additionalProperties = null)
    {
        if (null === $additionalProperties) {
            $additionalProperties = new stdClass;
        }

        $this->reference = $reference;
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
     * @return stdClass
     */
    public function additionalProperties()
    {
        return $this->additionalProperties;
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
    private $additionalProperties;
}
