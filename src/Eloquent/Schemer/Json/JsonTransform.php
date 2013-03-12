<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Json;

use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\IntegerValue;
use Eloquent\Schemer\Value\NumberValue;
use Eloquent\Schemer\Value\NullValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\ReferenceValue;
use Eloquent\Schemer\Value\StringValue;
use Exception;
use stdClass;

class JsonTransform
{
    /**
     * @param mixed $value
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function apply($value)
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
                return $this->transformArray($value);
            case 'object':
                return $this->transformObject($value);
        }

        throw new Exception(sprintf("Unsupported value type '%s'.", $type));
    }

    /**
     * @param array<integer,mixed> $value
     *
     * @return ArrayValue
     */
    public function transformArray(array $value)
    {
        foreach ($value as $index => $subValue) {
            $value[$index] = $this->apply($subValue);
        }

        return new ArrayValue($value);
    }

    /**
     * @param stdClass $value
     *
     * @return ObjectValue|ReferenceValue
     */
    public function transformObject(stdClass $value)
    {
        $value = clone $value;

        $isReference = false;
        foreach (get_object_vars($value) as $key => $subValue) {
            $value->{$key} = $this->apply($subValue);
            $isReference =
                $isReference ||
                '$ref' === $key && $value->{$key} instanceof StringValue
            ;
        }

        if ($isReference) {
            return new ReferenceValue($value);
        }

        return new ObjectValue($value);
    }
}
