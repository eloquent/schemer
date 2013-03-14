<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader;

use Eloquent\Enumeration\Multiton;

final class ContentType extends Multiton
{
    /**
     * @return array<string>
     */
    public function types()
    {
        return $this->types;
    }

    /**
     * @return string
     */
    public function primaryType()
    {
        $types = $this->types();

        return $types[0];
    }

    protected static function initializeMultiton()
    {
        parent::initializeMultiton();

        new static('JSON', array('application/json'));
        new static('TOML', array('application/x-toml'));
        new static('YAML', array('application/x-yaml'));
    }

    protected function __construct($key, array $types)
    {
        parent::__construct($key);

        $this->types = $types;
    }

    private $types;
}
