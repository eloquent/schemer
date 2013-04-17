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

class ReferenceValue extends AbstractObjectValue
{
    /**
     * @param UriInterface              $reference
     * @param PointerInterface|null     $pointer
     * @param string|null               $mimeType
     * @param ObjectValue|null          $additionalProperties
     * @param Factory\ValueFactory|null $factory
     */
    public function __construct(
        UriInterface $reference,
        PointerInterface $pointer = null,
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

        $this->reference = clone $reference;
        $this->reference->normalize();
        $this->pointer = $pointer;
        $this->mimeType = $mimeType;
        $this->additionalProperties = $additionalProperties;
        $this->factory = $factory;
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
     * @return Uri
     */
    public function uri()
    {
        $uri = new Uri($this->reference()->toString());
        $uri->setFragment($this->pointer()->string());

        return $uri;
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
        foreach (get_object_vars($value) as $property => $value) {
            $value->$property = $this->factory()->create($value);
        }

        return $value;
    }

    private $reference;
    private $pointer;
    private $mimeType;
    private $additionalProperties;
    private $factory;
}
