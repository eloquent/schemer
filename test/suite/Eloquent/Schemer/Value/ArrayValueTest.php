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

class ArrayValueTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new Factory\ValueFactory;
    }

    public function testValueRecursive()
    {
        $value = array('foo' => 'bar');
        $value['baz'] = &$value;
        $object = $this->factory->create($value);

        $this->assertSame($object, $object->baz);
    }
}
