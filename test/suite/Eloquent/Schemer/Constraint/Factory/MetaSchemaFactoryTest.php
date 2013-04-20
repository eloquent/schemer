<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Factory;

use PHPUnit_Framework_TestCase;

class MetaSchemaFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new MetaSchemaFactory;
    }

    public function testCreate()
    {
        $schema = $this->factory->create();

        $this->assertInstanceOf(
            'Eloquent\Schemer\Constraint\Schema',
            $schema
        );
    }
}
