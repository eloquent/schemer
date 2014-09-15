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
        //                       atoms               joinAtoms           resultAtoms
        return [
            'Single atom'    => [['atomA', 'atomB'], ['atomC'],          ['atomA', 'atomB', 'atomC']],
            'Multiple atoms' => [['atomA', 'atomB'], ['atomC', 'atomD'], ['atomA', 'atomB', 'atomC', 'atomD']],
        ];
    }

    /**
     * @dataProvider joinData
     */
    public function testJoin($atoms, $joinAtoms, $resultAtoms)
    {
        $pointer = new Pointer($atoms);
        $joined = $pointer->join(new Pointer($joinAtoms));

        $this->assertNotSame($pointer, $joined);
        $this->assertSame($resultAtoms, $joined->atoms());
    }

    public function testJoinEmpty()
    {
        $joined = $this->pointer->join(new Pointer);

        $this->assertSame($this->pointer, $joined);
    }

    /**
     * @dataProvider joinData
     */
    public function testJoinAtoms($atoms, $joinAtoms, $resultAtoms)
    {
        $pointer = new Pointer($atoms);
        $joined = call_user_func_array([$pointer, 'joinAtoms'], $joinAtoms);

        $this->assertNotSame($pointer, $joined);
        $this->assertSame($resultAtoms, $joined->atoms());
    }

    /**
     * @dataProvider joinData
     */
    public function testJoinAtomSequence($atoms, $joinAtoms, $resultAtoms)
    {
        $pointer = new Pointer($atoms);
        $joined = $pointer->joinAtomSequence($joinAtoms);

        $this->assertNotSame($pointer, $joined);
        $this->assertSame($resultAtoms, $joined->atoms());
    }

    public function testJoinAtomSequenceEmpty()
    {
        $joined = $this->pointer->joinAtomSequence([]);

        $this->assertSame($this->pointer, $joined);
    }

    public function parentData()
    {
        //                          atoms                        parentAtoms
        return [
            'Single atom'       => [['atomA'],                   []],
            'Single empty atom' => [[''],                        []],
            'Multiple atoms'    => [['atomA', 'atomB', 'atomC'], ['atomA', 'atomB']],
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
