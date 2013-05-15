<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference;

use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Pointer\PointerFactory;
use Eloquent\Schemer\Uri\UriFactory;
use FilesystemIterator;
use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SwitchingResolutionScopeMapFactoryTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->fixturePath = sprintf(
            '%s/../../../../fixture/reference/switching-scope-map',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->factory = new SwitchingResolutionScopeMapFactory;
        $this->reader = new Reader;
        $this->pointerFactory = new PointerFactory;
        $this->uriFactory = new UriFactory;
    }

    public function factoryData()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->fixturePath,
                FilesystemIterator::SKIP_DOTS
            )
        );

        $data = array();
        foreach ($iterator as $file) {
            $data[$file->getFilename()] = array($file->getFilename());
        }

        return $data;
    }

    /**
     * @dataProvider factoryData
     */
    public function testFactory($testName)
    {
        $path = sprintf('%s/%s', $this->fixturePath, $testName);
        $fixture = $this->reader->readPath($path);
        $expected = $fixture->expected->value();
        $map = $this->factory->create(
            $this->uriFactory->create('#'),
            $fixture->document
        );
        $actual = array();
        foreach ($map->map() as $tuple) {
            list($pointer, $uri) = $tuple;
            $actual[] = array($pointer->string(), $uri->toString());
        }

        $this->assertEquals($expected, $actual);
        $this->assertSame($expected, $actual);
        $this->assertSame($fixture->document, $map->value());
    }
}
