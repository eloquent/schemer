<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value;

use PHPUnit_Framework_TestCase;
use stdClass;

class ObjectValueTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new Factory\ValueFactory;
    }

    public function testValueRecursive()
    {
        $before = new stdClass;
        $before->foo = 'bar';
        $before->baz = $before;
        $instance = $this->factory->create($before);
        $after = $instance->value();

        $this->assertSame($after, $after->baz);
    }
}
