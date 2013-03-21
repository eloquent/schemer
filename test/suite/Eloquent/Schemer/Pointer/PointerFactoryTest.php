<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer;

use Eloquent\Equality\Comparator;
use PHPUnit_Framework_TestCase;

class PointerFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = new PointerFactory;
        $this->comparator = new Comparator;
    }

    public function createData()
    {
        return array(
            'Empty pointer' => array(
                '',
                array(),
            ),
            'Pointer with single empty string atom' => array(
                '/',
                array(''),
            ),
            'Escaped characters in atoms' => array(
                '/foo/b~0a~1r/b~1a~0z/q~01ux',
                array('foo', 'b~a/r', 'b/a~z', 'q~1ux'),
            ),
        );
    }

    /**
     * @dataProvider createData
     */
    public function testCreate($pointer, array $expectedAtoms)
    {
        $actual = $this->factory->create($pointer);
        $expected = new Pointer($expectedAtoms);

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }

    public function createFailureData()
    {
        return array(
            'No leading separator' => array('foo'),
        );
    }

    /**
     * @dataProvider createFailureData
     */
    public function testCreateFailure($pointer)
    {
        $this->setExpectedException(
            __NAMESPACE__.'\Exception\InvalidPointerException'
        );
        $this->factory->create($pointer);
    }
}
