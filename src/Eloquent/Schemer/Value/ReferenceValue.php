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

class ReferenceValue extends AbstractObjectValue
{
    /**
     * @param UriInterface              $uri
     * @param string|null               $mimeType
     * @param ObjectValue|null          $additionalProperties
     * @param Factory\ValueFactory|null $factory
     */
    public function __construct(
        UriInterface $uri,
        $mimeType = null,
        ObjectValue $additionalProperties = null,
        Factory\ValueFactory $factory = null
    ) {
        if (null === $additionalProperties) {
            $additionalProperties = new ObjectValue;
        }
        if (null === $factory) {
            $factory = new Factory\ValueFactory;
        }

        $this->uri = clone $uri;
        $this->uri->normalize();
        $this->mimeType = $mimeType;
        $this->additionalProperties = $additionalProperties;
        $this->factory = $factory;
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
     * @return ObjectValue
     */
    public function additionalProperties()
    {
        return $this->additionalProperties;
    }

    /**
     * @return Factory\ValueFactory
     */
    public function factory()
    {
        return $this->factory;
    }

    /**
     * @return stdClass
     */
    public function value()
    {
        $value = $this->additionalProperties()->value();
        $value->{'$ref'} = $this->uri()->toString();
        if (null !== $this->mimeType()) {
            $value->{'$type'} =  $this->mimeType();
        }

        return $value;
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

    /**
     * @return stdClass
     */
    protected function wrappedValue()
    {
        $value = $this->value();
        foreach (get_object_vars($value) as $property => $subValue) {
            $value->$property = $this->factory()->create($subValue);
        }

        return $value;
    }

    private $uri;
    private $mimeType;
    private $additionalProperties;
    private $factory;
}
