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

use Exception;

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
            case 'array':
                return $this->transformArray($value);
            case 'boolean':
                return $this->transformBoolean($value);
            case 'integer':
                return $this->transformInteger($value);
            case 'double':
                return $this->transformNumber($value);
            case 'NULL':
                return new NullValue;
            case 'object':
                return $this->transformObject($value);
            case 'string':
                return $this->transformString($value);
        }

        throw new Exception(sprintf("Unsupported value type '%s'.", $type));
    }
}
