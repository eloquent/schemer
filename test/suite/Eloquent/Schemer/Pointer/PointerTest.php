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

use PHPUnit_Framework_TestCase;

class PointerTest extends PHPUnit_Framework_TestCase
{
    public function stringData()
    {
        return array(
            'Empty pointer' => array(
                array(),
                '',
            ),
            'Pointer with single empty string atom' => array(
                array(''),
                '/',
            ),
            'Escaped characters in atoms' => array(
                array('foo', 'b~a/r', 'b/a~z', 'q~1ux'),
                '/foo/b~0a~1r/b~1a~0z/q~01ux',
            ),
        );
    }

    /**
     * @dataProvider stringData
     */
    public function testString(array $atoms, $expected)
    {
        $pointer = new Pointer($atoms);

        $this->assertSame($expected, $pointer->string());
    }
}
