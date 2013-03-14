<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Yaml;

use Eloquent\Schemer\Serialization\SerializationProtocolInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlProtocol implements SerializationProtocolInterface
{
    /**
     * @param Parser|null $parser
     */
    public function __construct(Parser $parser = null)
    {
        if (null === $parser) {
            $parser = new Parser;
        }

        $this->parser = $parser;
    }

    /**
     * @return Parser
     */
    public function parser()
    {
        return $this->parser;
    }

    /**
     * @param string $data
     *
     * @return mixed
     * @throws Exception\YamlThawException
     */
    public function thaw($data)
    {
        try {
            $value = $this->parser()->parse($data, true);
        } catch (ParseException $e) {
            throw new Exception\YamlThawException($e);
        }

        return $value;
    }

    private $parser;
}
