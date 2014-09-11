<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Json;

use Eloquent\Liberator\Liberator;
use Exception;
use Icecave\Isolator\Isolator;
use PHPUnit_Framework_TestCase;
use Phake;

class JsonDataSerializerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->serializeOptions = 0;
        $this->isolator = Phake::partialMock(Isolator::className());
        $this->serializer = new JsonDataSerializer($this->serializeOptions, $this->isolator);

        $this->value = (object) ['foo' => "b\xC3\xA4r b/az"];
        $this->data = '{"foo":"b\u00e4r b\/az"}';
    }

    public function testConstructor()
    {
        $this->assertSame($this->serializeOptions, $this->serializer->serializeOptions());
    }

    public function testConstructorDefaults()
    {
        $this->serializer = new JsonDataSerializer;

        $this->assertSame(
             JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            $this->serializer->serializeOptions()
        );
    }

    public function testSerialize()
    {
        $this->assertSame($this->data, $this->serializer->serialize($this->value));
    }

    public function testSerializeWithDefaultOptions()
    {
        $this->serializer = new JsonDataSerializer;

        $this->assertSame("{\n    \"foo\": \"b\xC3\xA4r b/az\"\n}", $this->serializer->serialize($this->value));
    }

    public function testSerializeFailure()
    {
        Phake::when($this->isolator)->json_last_error()->thenReturn(JSON_ERROR_SYNTAX);
        $exception = null;
        try {
            $this->serializer->serialize('value');
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Serialization\Exception\SerializeException', $exception);
        $this->assertInstanceOf(
            'Eloquent\Schemer\Serialization\Json\Exception\JsonException',
            $exception->getPrevious()
        );
        $this->assertSame("JSON error: Syntax error.", $exception->getPrevious()->getMessage());
    }

    public function testUnserialize()
    {
        $this->assertEquals($this->value, $this->serializer->unserialize($this->data));
    }

    public function testUnserializeFailure()
    {
        $exception = null;
        try {
            $this->serializer->unserialize('{');
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Serialization\Exception\UnserializeException', $exception);
        $this->assertInstanceOf(
            'Eloquent\Schemer\Serialization\Json\Exception\JsonException',
            $exception->getPrevious()
        );
        $this->assertSame("JSON error: Syntax error.", $exception->getPrevious()->getMessage());
    }

    public function testInstance()
    {
        $class = get_class($this->serializer);
        Liberator::liberateClass($class)->instance = null;
        $actual = $class::instance();

        $this->assertInstanceOf($class, $actual);
        $this->assertSame($actual, $class::instance());
    }
}
