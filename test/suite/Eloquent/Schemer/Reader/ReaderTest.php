<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reader;

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\StringValue;
use PHPUnit_Framework_TestCase;
use stdClass;

class ReaderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->reader = new Reader;

        $this->comparator = new Comparator;
    }

    public function testReadStringJson()
    {
        $data = '["foo", "bar"]';
        $expected = new ArrayValue(array(
            new StringValue('foo'),
            new StringValue('bar'),
        ));
        $actual = $this->reader->readString($data);

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }

    public function testReadStringToml()
    {
        $data = 'foo = "bar"';
        $expectedObject = new stdClass;
        $expectedObject->foo = new StringValue('bar');
        $expected = new ObjectValue($expectedObject);
        $actual = $this->reader->readString($data, 'application/x-toml');

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }

    public function testReadStringYaml()
    {
        $data = '[foo, bar]';
        $expected = new ArrayValue(array(
            new StringValue('foo'),
            new StringValue('bar'),
        ));
        $actual = $this->reader->readString($data, 'application/x-yaml');

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }
}
