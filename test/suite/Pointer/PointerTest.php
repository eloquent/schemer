<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer;

use PHPUnit_Framework_TestCase;

class PointerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->atoms = ['atomA', 'atomB', 'atomC'];
        $this->pointer = new Pointer(['atomA', 'atomB', 'atomC']);
    }

    public function testConstructor()
    {
        $this->assertSame($this->atoms, $this->pointer->atoms());
        $this->assertSame('atomC', $this->pointer->lastAtom());
        $this->assertTrue($this->pointer->hasAtoms());
        $this->assertSame(3, $this->pointer->size());
    }

    public function testConstructorDefaults()
    {
        $this->pointer = new Pointer;

        $this->assertSame([], $this->pointer->atoms());
        $this->assertNull($this->pointer->lastAtom());
        $this->assertFalse($this->pointer->hasAtoms());
        $this->assertSame(0, $this->pointer->size());
    }

    public function joinData()
    {
        //                       joinAtoms                    atoms
        return [
            'Single atom'    => [['atomD'],                   ['atomA', 'atomB', 'atomC', 'atomD']],
            'Multiple atoms' => [['atomD', 'atomE', 'atomF'], ['atomA', 'atomB', 'atomC', 'atomD', 'atomE', 'atomF']],
        ];
    }

    /**
     * @dataProvider joinData
     */
    public function testJoin($joinAtoms, $atoms)
    {
        $pointer = $this->pointer->join(new Pointer($joinAtoms));

        $this->assertNotSame($this->pointer, $pointer);
        $this->assertSame($atoms, $pointer->atoms());
    }

    public function testJoinEmpty()
    {
        $pointer = $this->pointer->join(new Pointer);

        $this->assertSame($this->pointer, $pointer);
    }

    /**
     * @dataProvider joinData
     */
    public function testJoinAtoms($joinAtoms, $atoms)
    {
        $pointer = call_user_func_array([$this->pointer, 'joinAtoms'], $joinAtoms);

        $this->assertNotSame($this->pointer, $pointer);
        $this->assertSame($atoms, $pointer->atoms());
    }

    /**
     * @dataProvider joinData
     */
    public function testJoinAtomSequence($joinAtoms, $atoms)
    {
        $pointer = $this->pointer->joinAtomSequence($joinAtoms);

        $this->assertNotSame($this->pointer, $pointer);
        $this->assertSame($atoms, $pointer->atoms());
    }

    public function testJoinAtomSequenceEmpty()
    {
        $pointer = $this->pointer->joinAtomSequence([]);

        $this->assertSame($this->pointer, $pointer);
    }

    public function parentData()
    {
        //                       atoms                        parentAtoms
        return [
            'Single atom'    => [['atomA'],                   ['']],
            'Multiple atoms' => [['atomA', 'atomB', 'atomC'], ['atomA', 'atomB']],
        ];
    }

    /**
     * @dataProvider parentData
     */
    public function testParent($atoms, $parentAtoms)
    {
        $pointer = new Pointer($atoms);
        $parent = $pointer->parent();

        $this->assertNotSame($pointer, $parent);
        $this->assertSame($parentAtoms, $parent->atoms());
    }

    public function testParentEmpty()
    {
        $pointer = new Pointer;

        $this->assertNull($pointer->parent());
    }

    public function stringData()
    {
        //                           atoms                               string
        return [
            'Empty'              => [[],                                 ''],
            'Single empty atom'  => [[''],                               '/'],
            'Escaped characters' => [['foo', 'b~a/r', 'b/a~z', 'q~1ux'], '/foo/b~0a~1r/b~1a~0z/q~01ux'],
        ];
    }

    /**
     * @dataProvider stringData
     */
    public function testString($atoms, $string)
    {
        $pointer = new Pointer($atoms);

        $this->assertSame($string, $pointer->string());
        $this->assertSame($string, strval($pointer));
    }
}
