<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Toml;

use Eloquent\Schemer\Reader\AbstractReader;
use Eloquent\Schemer\Value\Transform\AssociativeValueTransform;
use Eloquent\Schemer\Value\Transform\ValueTransformInterface;
use Toml\Parser;

class TomlStringReader extends AbstractReader
{
    /**
     * @param string                       $data
     * @param ValueTransformInterface|null $transform
     */
    public function __construct(
        $data,
        ValueTransformInterface $transform = null
    ) {
        if (null === $transform) {
            $transform = new AssociativeValueTransform;
        }

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
        return $this->transform()->apply(
            Parser::fromString($this->data())
        );
    }

    private $data;
}
