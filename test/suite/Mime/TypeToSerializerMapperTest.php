<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Mime;

use Eloquent\Liberator\Liberator;
use Phake;
use PHPUnit_Framework_TestCase;

class TypeToSerializerMapperTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->serializerA = Phake::mock('Eloquent\Schemer\Serialization\DataSerializerInterface');
        $this->serializerB = Phake::mock('Eloquent\Schemer\Serialization\DataSerializerInterface');
        $this->typeMap = [
            'a' => $this->serializerA,
            'b' => $this->serializerB,
        ];
        $this->mapper = new TypeToSerializerMapper($this->typeMap);

        $this->serializerC = Phake::mock('Eloquent\Schemer\Serialization\DataSerializerInterface');
    }

    public function testConstructor()
    {
        $this->assertSame($this->typeMap, $this->mapper->typeMap());
    }

    public function testConstructorDefaults()
    {
        $this->mapper = new TypeToSerializerMapper;

        $this->assertSame(TypeToSerializerMapper::defaultTypeMap(), $this->mapper->typeMap());
    }

    public function testSetTypeMap()
    {
        $this->mapper->setTypeMap(TypeToSerializerMapper::defaultTypeMap());

        $this->assertSame(TypeToSerializerMapper::defaultTypeMap(), $this->mapper->typeMap());
    }

    public function testSetTypeMapEntry()
    {
        $this->mapper->setTypeMapEntry('c', $this->serializerC);

        $this->assertSame($this->serializerC, $this->mapper->serializerByType('c'));
    }

    public function testRemoveTypeMapEntry()
    {
        $this->assertSame($this->serializerA, $this->mapper->serializerByType('a'));
        $this->assertTrue($this->mapper->removeTypeMapEntry('a'));
        $this->assertFalse($this->mapper->removeTypeMapEntry('c'));

        $this->setExpectedException('Eloquent\Schemer\Mime\Exception\UnsupportedMimeTypeException');
        $this->mapper->serializerByType('a');
    }

    public function serializerByTypeData()
    {
        //             type                serializerClass
        return [
            'JSON' => ['application/json', 'Eloquent\Schemer\Serialization\Json\JsonDataSerializer'],
        ];
    }

    /**
     * @dataProvider serializerByTypeData
     */
    public function testSerializerByType($type, $serializerClass)
    {
        $this->mapper = new TypeToSerializerMapper;

        $this->assertSame($serializerClass::instance(), $this->mapper->serializerByType($type));
    }

    public function testInstance()
    {
        $class = get_class($this->mapper);
        Liberator::liberateClass($class)->instance = null;
        $actual = $class::instance();

        $this->assertInstanceOf($class, $actual);
        $this->assertSame($actual, $class::instance());
    }
}
