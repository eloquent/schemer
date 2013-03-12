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

use Eloquent\Schemer\Reader\AbstractReader;
use Eloquent\Schemer\Value\Transform\ValueTransformInterface;

class JsonStringReader extends AbstractReader
{
    /**
     * @param string                       $data
     * @param ValueTransformInterface|null $transform
     */
    public function __construct(
        $data,
        ValueTransformInterface $transform = null
    ) {
        parent::__construct($transform);

        $this->data = $data;
    }

    /**
     * @return string
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function read()
    {
        $value = json_decode($this->data());
        $error = json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            throw new Exception\JsonParseException($error);
        }

        return $this->transform()->apply($value);
    }

    private $data;
}
