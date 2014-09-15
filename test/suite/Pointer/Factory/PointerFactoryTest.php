<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Factory;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class PointerFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->factory = new PointerFactory;
    }

    public function createData()
    {
        //                           string                         atoms
        return [
            'Empty'              => ['',                            []],
            'Single empty atom'  => ['/',                           ['']],
            'Escaped characters' => ['/foo/b~0a~1r/b~1a~0z/q~01ux', ['foo', 'b~a/r', 'b/a~z', 'q~1ux']],
        ];
    }

    /**
     * @dataProvider createData
     */
    public function testCreate($string, $atoms)
    {
        $pointer = $this->factory->create($string);

        $this->assertSame($atoms, $pointer->atoms());
    }

    public function testCreateWithNull()
    {
        $this->assertNull($this->factory->create(null));
    }

    public function testCreateFailureNotAbsolute()
    {
        $this->setExpectedException('Eloquent\Schemer\Pointer\Factory\Exception\InvalidPointerException');
        $this->factory->create('atomA/atomB');
    }

    public function createFromUriData()
    {
        //                           uri                                                atoms
        return [
            'Missing'            => ['http://example.org/',                             []],
            'Empty'              => ['http://example.org/#',                            []],
            'Single empty atom'  => ['http://example.org/#/',                           ['']],
            'Escaped characters' => ['http://example.org/#/foo/b~0%61%7E1r/b~1a~0z%2Fq~01ux', ['foo', 'b~a/r', 'b/a~z', 'q~1ux']],
        ];
    }

    /**
     * @dataProvider createFromUriData
     */
    public function testCreateFromUri($uri, $atoms)
    {
        $pointer = $this->factory->createFromUri($uri);

        $this->assertSame($atoms, $pointer->atoms());
    }

    public function testCreateFromAtoms()
    {
        $atoms = ['atomA', 'atomB'];
        $pointer = $this->factory->createFromAtoms($atoms);

        $this->assertSame($atoms, $pointer->atoms());
    }

    public function testInstance()
    {
        $class = get_class($this->factory);
        Liberator::liberateClass($class)->instance = null;
        $actual = $class::instance();

        $this->assertInstanceOf($class, $actual);
        $this->assertSame($actual, $class::instance());
    }
}
