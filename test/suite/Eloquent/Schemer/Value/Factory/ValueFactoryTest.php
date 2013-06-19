<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value\Factory;

use PHPUnit_Framework_TestCase;
use stdClass;

class ValueFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new ValueFactory;
    }

    public function testCreateRecursiveArray()
    {
        $value = array('foo' => 'bar');
        $value['baz'] = &$value;
        $instance = $this->factory->create($value);

        $this->assertSame($instance, $instance->baz);
    }

    public function testCreateSimilarNonRecursiveArray()
    {
        $value = array(
            'foo' => array('bar', 'baz'),
            'qux' => array('bar', 'baz'),
        );
        $instance = $this->factory->create($value);

        $this->assertNotSame($instance->foo, $instance->qux);
    }

    public function testCreateRecursiveObject()
    {
        $value = new stdClass;
        $value->foo = 'bar';
        $value->baz = $value;
        $instance = $this->factory->create($value);

        $this->assertSame($instance, $instance->baz);
    }

    public function testCreateSimilarNonRecursiveObject()
    {
        $value = new stdClass;
        $value->foo = 'bar';
        $value->baz = new stdClass;
        $value->baz->foo = 'bar';
        $value->baz->baz = $value->baz;
        $instance = $this->factory->create($value);

        $this->assertNotSame($instance, $instance->baz);
    }

    public function testCreateNestedRecursiveValue()
    {
        $value = array('foo' => 'bar');
        $value['baz'] = new stdClass;
        $value['baz']->qux = &$value;
        $instance = $this->factory->create($value);

        $this->assertSame($instance, $instance->baz->qux);
        $this->assertSame($instance->baz, $instance->baz->qux->baz);
    }
}
