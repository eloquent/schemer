<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Yaml;

use Eloquent\Schemer\Reader\AbstractReader;
use Eloquent\Schemer\Value\ValueTransformInterface;
use Symfony\Component\Yaml\Parser;

class YamlStringReader extends AbstractReader
{
    /**
     * @param string                       $data
     * @param Parser|null                  $parser
     * @param ValueTransformInterface|null $transform
     */
    public function __construct(
        $data,
        Parser $parser = null,
        ValueTransformInterface $transform = null
    ) {
        if (null === $parser) {
            $parser = new Parser;
        }
        if (null === $transform) {
            $transform = new YamlTransform;
        }

        parent::__construct($transform);

        $this->parser = $parser;
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
     * @return Parser
     */
    public function parser()
    {
        return $this->parser;
    }

    /**
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function read()
    {
        return $this->transform()->apply(
            $this->parser()->parse($this->data(), true)
        );
    }

    private $data;
    private $parser;
}
