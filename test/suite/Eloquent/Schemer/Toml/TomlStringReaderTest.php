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

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Value\ArrayValue;
use Eloquent\Schemer\Value\BooleanValue;
use Eloquent\Schemer\Value\IntegerValue;
use Eloquent\Schemer\Value\NumberValue;
use Eloquent\Schemer\Value\ObjectValue;
use Eloquent\Schemer\Value\ReferenceValue;
use Eloquent\Schemer\Value\StringValue;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers \Eloquent\Schemer\Toml\TomlStringReader
 * @covers \Eloquent\Schemer\Reader\AbstractReader
 */
class TomlStringReaderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_comparator = new Comparator;
    }

    public function testReader()
    {
        $toml = <<<'EOD'
foo = true
bar = 111
baz = 1.11
qux = ["doom", "splat"]
[ping]
$ref = "pong"
pang = "peng"
EOD;
        $reader = new TomlStringReader($toml);
        $expectedReference = new stdClass;
        $expectedReference->{'$ref'} = new StringValue('pong');
        $expectedReference->pang = new StringValue('peng');
        $expectedObject = new stdClass;
        $expectedObject->foo = new BooleanValue(true);
        $expectedObject->bar = new IntegerValue(111);
        $expectedObject->baz = new NumberValue(1.11);
        $expectedObject->qux = new ArrayValue(array(
            new StringValue('doom'),
            new StringValue('splat'),
        ));
        $expectedObject->ping = new ReferenceValue($expectedReference);
        $expected = new ObjectValue($expectedObject);
        $actual = $reader->read();

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->_comparator->equals($expected, $actual));
    }

    public function testReaderFailureSyntaxError()
    {
        $toml = 'foo =';
        $reader = new TomlStringReader($toml);

        $this->setExpectedException('Exception');
        $reader->read();
    }
}
