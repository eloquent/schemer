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
use PHPUnit_Framework_TestCase;

class PathToTypeMapperTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->extensionMap = [
            'a' => 'type-a',
            'b' => 'type-b',
        ];
        $this->mapper = new PathToTypeMapper($this->extensionMap);
    }

    public function testConstructor()
    {
        $this->assertSame($this->extensionMap, $this->mapper->extensionMap());
    }

    public function testConstructorDefaults()
    {
        $this->mapper = new PathToTypeMapper;

        $this->assertSame(PathToTypeMapper::defaultExtensionMap(), $this->mapper->extensionMap());
    }

    public function testSetExtensionMap()
    {
        $this->mapper->setExtensionMap(PathToTypeMapper::defaultExtensionMap());

        $this->assertSame(PathToTypeMapper::defaultExtensionMap(), $this->mapper->extensionMap());
    }

    public function testSetExtensionMapEntry()
    {
        $this->assertNull($this->mapper->typeByPath('/path/to/file.extension'));

        $this->mapper->setExtensionMapEntry('extension', 'type');

        $this->assertSame('type', $this->mapper->typeByPath('/path/to/file.extension'));
    }

    public function testRemoveExtensionMapEntry()
    {
        $this->assertSame('type-a', $this->mapper->typeByPath('/path/to/file.a'));
        $this->assertTrue($this->mapper->removeExtensionMapEntry('a'));
        $this->assertNull($this->mapper->typeByPath('/path/to/file.a'));
        $this->assertFalse($this->mapper->removeExtensionMapEntry('a'));
    }

    public function typeByPathData()
    {
        //                          path                  mimeType
        return [
            'JSON'              => ['/path/to/file.json', 'application/json'],
            'TOML'              => ['/path/to/file.toml', 'text/x-toml'],
            'YAML short'        => ['/path/to/file.yml',  'application/x-yaml'],
            'YAML long'         => ['/path/to/file.yaml', 'application/x-yaml'],

            'Unknown extension' => ['/path/to/file.xxx',  null],
            'No extension'      => ['/path/to/file',      null],
        ];
    }

    /**
     * @dataProvider typeByPathData
     */
    public function testTypeByPath($path, $mimeType)
    {
        $this->mapper = new PathToTypeMapper;

        $this->assertSame($mimeType, $this->mapper->typeByPath($path));
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
