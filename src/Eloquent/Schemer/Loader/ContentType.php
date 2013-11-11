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

use Eloquent\Enumeration\AbstractMultiton;

final class ContentType extends AbstractMultiton
{
    /**
     * @return array<string>
     */
    public function mimeTypes()
    {
        return $this->mimeTypes;
    }

    /**
     * @return string
     */
    public function primaryMimeType()
    {
        $mimeTypes = $this->mimeTypes();

        return $mimeTypes[0];
    }

    protected static function initializeMembers()
    {
        new static('JSON', array('application/json'));
        new static('TOML', array('application/x-toml'));
        new static('YAML', array('application/x-yaml'));
    }

    protected function __construct($key, array $mimeTypes)
    {
        parent::__construct($key);

        $this->mimeTypes = $mimeTypes;
    }

    private $mimeTypes;
}
