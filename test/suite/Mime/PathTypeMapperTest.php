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

class PathTypeMapperTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->mapper = new PathTypeMapper;
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
