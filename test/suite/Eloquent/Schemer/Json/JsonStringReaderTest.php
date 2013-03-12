<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Json;

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\IntegerValue;
use Eloquent\Schemer\Value\NumberValue;
use Eloquent\Schemer\Value\NullValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\ReferenceValue;
use Eloquent\Schemer\Value\StringValue;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \Eloquent\Schemer\Json\JsonStringReader
 * @covers \Eloquent\Schemer\Json\AbstractJsonReader
 */
class JsonStringReaderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_comparator = new Comparator;
    }

    public function testReader()
    {
        $json = '{"foo": [true, 111, null, 1.11, "bar", {"$ref": "baz", "qux": "doom"}]}';
        $reader = new JsonStringReader($json);
        $expectedReference = new stdClass;
        $expectedReference->{'$ref'} = new StringValue('baz');
        $expectedReference->qux = new StringValue('doom');
        $expectedObject = new stdClass;
        $expectedObject->foo = new ArrayValue(array(
            new BooleanValue(true),
            new IntegerValue(111),
            new NullValue,
            new NumberValue(1.11),
            new StringValue('bar'),
            new ReferenceValue($expectedReference)
        ));
        $expected = new ObjectValue($expectedObject);
        $actual = $reader->read();

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->_comparator->equals($expected, $actual));
    }

    public function testReaderFailureSyntaxError()
    {
        $json = '{';
        $reader = new JsonStringReader($json);

        $this->setExpectedException(
            __NAMESPACE__.'\Exception\JsonParseException',
            'Unable to parse JSON. Syntax error.'
        );
        $reader->read();
    }
}
