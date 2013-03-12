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

class JsonStringReader extends AbstractJsonReader
{
    /**
     * @param string             $data
     * @param JsonTransform|null $transform
     */
    public function __construct($data, JsonTransform $transform = null)
    {
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

        return $this->transform()->apply(json_decode($this->data()));
    }

    private $data;
}
