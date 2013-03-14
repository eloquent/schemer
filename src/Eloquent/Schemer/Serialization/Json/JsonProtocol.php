<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Json;

use Eloquent\Schemer\Serialization\SerializationProtocolInterface;

class JsonProtocol implements SerializationProtocolInterface
{
    /**
     * @param string $data
     *
     * @return mixed
     * @throws Exception\JsonThawException
     */
    public function thaw($data)
    {
        $value = json_decode($data);
        $error = json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            throw new Exception\JsonThawException($error);
        }

        return $this->transform()->apply($value);
    }
}
