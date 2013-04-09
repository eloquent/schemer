<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value\Factory;

use DateTime;
use Eloquent\Schemer\Pointer\PointerFactory;
use Eloquent\Schemer\Pointer\PointerFactoryInterface;
use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\DateTimeValue;
use Eloquent\Schemer\Value\IntegerValue;
use Eloquent\Schemer\Value\NullValue;
use Eloquent\Schemer\Value\NumberValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\StringValue;
use Eloquent\Schemer\Value\ReferenceValue;
use InvalidArgumentException;
use stdClass;
use Zend\Uri\Uri;

class ValueFactory implements ValueFactoryInterface
{
    /**
     * @param UriFactoryInterface|null     $uriFactory
     * @param PointerFactoryInterface|null $pointerFactory
     */
    public function __construct(
        UriFactoryInterface $uriFactory = null,
        PointerFactoryInterface $pointerFactory = null
    ) {
        if (null === $uriFactory) {
            $uriFactory = new UriFactory;
        }
        if (null === $pointerFactory) {
            $pointerFactory = new PointerFactory;
        }

        $this->uriFactory = $uriFactory;
        $this->pointerFactory = $pointerFactory;
    }

    /**
     * @return UriFactoryInterface
     */
    public function uriFactory()
    {
        return $this->uriFactory;
    }

    /**
     * @return PointerFactoryInterface
     */
    public function pointerFactory()
    {
        return $this->pointerFactory;
    }

    /**
     * @param mixed $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function create($value)
    {
        $type = gettype($value);
        switch ($type) {
            case 'boolean':
                return new BooleanValue($value);
            case 'integer':
                return new IntegerValue($value);
            case 'double':
                return new NumberValue($value);
            case 'NULL':
                return new NullValue;
            case 'string':
                return new StringValue($value);
            case 'array':
                return $this->createArray($value);
            case 'object':
                if ($value instanceof DateTime) {
                    return new DateTimeValue($value);
                }

                return $this->createObject($value);
        }

        throw new InvalidArgumentException(
            sprintf("Unsupported value type '%s'.", $type)
        );
    }

    /**
     * @param array<integer,mixed> $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    protected function createArray(array $value)
    {
        $isObject = false;
        $expectedIndex = 0;
        foreach ($value as $index => $subValue) {
            $value[$index] = $this->create($subValue);
            $isObject = $isObject || $index !== $expectedIndex++;
        }

        if ($isObject) {
            $object = new stdClass;
            foreach ($value as $key => $subValue) {
                $object->$key = $subValue;
            }

            return $this->createReference($object);
        }

        return new ArrayValue($value);
    }

    /**
     * @param stdClass $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    protected function createObject(stdClass $value)
    {
        $value = clone $value;

        foreach (get_object_vars($value) as $property => $subValue) {
            if ('' === $property) {
                $property = '_empty_';
            }
            $value->$property = $this->create($subValue);
        }

        return $this->createReference($value);
    }

    /**
     * @param stdClass $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    protected function createReference(stdClass $value)
    {
        if (
            property_exists($value, '$ref') &&
            $value->{'$ref'} instanceof StringValue
        ) {
            $uri = new Uri($value->{'$ref'}->value());
            unset($value->{'$ref'});

            $pointer = null;
            if (null !== $uri->getFragment()) {
                $pointer = $this->pointerFactory()->create($uri->getFragment());
                $uri->setFragment(null);
            }
            $reference = $this->uriFactory()->create($uri->toString());

            $type = null;
            if (
                property_exists($value, '$type') &&
                $value->{'$type'} instanceof StringValue
            ) {
                $type = $value->{'$type'}->value();
                unset($value->{'$type'});
            }

            return new ReferenceValue($reference, $pointer, $type, $value);
        }

        return new ObjectValue($value);
    }

    private $uriFactory;
    private $pointerFactory;
}
