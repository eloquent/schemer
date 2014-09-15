<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Resolver;

use Eloquent\Liberator\Liberator;
use Eloquent\Schemer\Pointer\Pointer;
use PHPUnit_Framework_TestCase;

class BoundObjectMapPointerResolverTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->value = (object) [
            'propertyA' => (object) [
                'propertyAA' => [
                    'valueAAA',
                    'valueAAB',
                    (object) [],
                ],
            ],
            'propertyB' => 'valueB',
        ];
        $this->resolver = new BoundObjectMapPointerResolver($this->value);
    }

    public function testConstructor()
    {
        $this->assertSame($this->value, $this->resolver->value());
    }

    public function testResolve()
    {
        $this->assertSame([$this->value, true], $this->resolver->resolve(Pointer::create('')));
        $this->assertSame([$this->value->propertyA, true], $this->resolver->resolve(Pointer::create('/propertyA')));
        $this->assertSame(
            [$this->value->propertyA->propertyAA, true],
            $this->resolver->resolve(Pointer::create('/propertyA/propertyAA'))
        );
        $this->assertSame(
            [$this->value->propertyA->propertyAA[0], true],
            $this->resolver->resolve(Pointer::create('/propertyA/propertyAA/0'))
        );
        $this->assertSame(
            [$this->value->propertyA->propertyAA[1], true],
            $this->resolver->resolve(Pointer::create('/propertyA/propertyAA/1'))
        );
        $this->assertSame(
            [$this->value->propertyA->propertyAA[2], true],
            $this->resolver->resolve(Pointer::create('/propertyA/propertyAA/2'))
        );
        $this->assertSame([$this->value->propertyB, true], $this->resolver->resolve(Pointer::create('/propertyB')));
        $this->assertSame([null, false], $this->resolver->resolve(Pointer::create('/')));
        $this->assertSame([null, false], $this->resolver->resolve(Pointer::create('/PROPERTYA')));
        $this->assertSame([null, false], $this->resolver->resolve(Pointer::create('/propertyC')));
        $this->assertSame([null, false], $this->resolver->resolve(Pointer::create('/propertyB/propertyC')));
        $this->assertSame([null, false], $this->resolver->resolve(Pointer::create('/propertyAA')));
        $this->assertSame(
            [
                '/propertyA' => $this->value->propertyA,
                '/propertyA/propertyAA' => $this->value->propertyA->propertyAA,
                '/propertyA/propertyAA/0' => $this->value->propertyA->propertyAA[0],
                '/propertyA/propertyAA/1' => $this->value->propertyA->propertyAA[1],
                '/propertyA/propertyAA/2' => $this->value->propertyA->propertyAA[2],
                '/propertyB' => $this->value->propertyB,
            ],
            Liberator::liberate($this->resolver)->index
        );
    }

    public function testResolveReturnsReference()
    {
        $result = $this->resolver->resolve(Pointer::create('/propertyA/propertyAA'));
        $result[0][] = 'valueAAC';

        $this->assertTrue(in_array('valueAAC', $this->value->propertyA->propertyAA, true));
    }
}
