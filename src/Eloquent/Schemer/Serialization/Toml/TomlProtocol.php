<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Toml;

use Eloquent\Schemer\Serialization\SerializationProtocolInterface;
use Exception;
use Toml\Parser;

class TomlProtocol implements SerializationProtocolInterface
{
    /**
     * @param string $data
     *
     * @return mixed
     * @throws Exception\TomlThawException
     */
    public function thaw($data)
    {
        try {
            $value = Parser::fromString($data);
        } catch (Exception $e) {
            throw new Exception\TomlThawException($e);
        }

        return $value;
    }

    private $data;
}
