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

class ObjectMapPointerResolverTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->resolver = new ObjectMapPointerResolver;

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
    }

    public function testResolve()
    {
        $this->assertSame([$this->value, true], $this->resolver->resolve($this->value, Pointer::create('')));
        $this->assertSame(
            [$this->value->propertyA, true],
            $this->resolver->resolve($this->value, Pointer::create('/propertyA'))
        );
        $this->assertSame(
            [$this->value->propertyA->propertyAA, true],
            $this->resolver->resolve($this->value, Pointer::create('/propertyA/propertyAA'))
        );
        $this->assertSame(
            [$this->value->propertyA->propertyAA[0], true],
            $this->resolver->resolve($this->value, Pointer::create('/propertyA/propertyAA/0'))
        );
        $this->assertSame(
            [$this->value->propertyA->propertyAA[1], true],
            $this->resolver->resolve($this->value, Pointer::create('/propertyA/propertyAA/1'))
        );
        $this->assertSame(
            [$this->value->propertyA->propertyAA[2], true],
            $this->resolver->resolve($this->value, Pointer::create('/propertyA/propertyAA/2'))
        );
        $this->assertSame(
            [$this->value->propertyB, true],
            $this->resolver->resolve($this->value, Pointer::create('/propertyB'))
        );
        $this->assertSame([null, false], $this->resolver->resolve($this->value, Pointer::create('/')));
        $this->assertSame([null, false], $this->resolver->resolve($this->value, Pointer::create('/PROPERTYA')));
        $this->assertSame([null, false], $this->resolver->resolve($this->value, Pointer::create('/propertyC')));
        $this->assertSame(
            [null, false],
            $this->resolver->resolve($this->value, Pointer::create('/propertyB/propertyC'))
        );
        $this->assertSame([null, false], $this->resolver->resolve($this->value, Pointer::create('/propertyAA')));
    }

    public function testResolveReturnsReference()
    {
        $result = $this->resolver->resolve($this->value, Pointer::create('/propertyA/propertyAA'));
        $result[0][] = 'valueAAC';

        $this->assertTrue(in_array('valueAAC', $this->value->propertyA->propertyAA, true));
    }

    public function testInstance()
    {
        $class = get_class($this->resolver);
        Liberator::liberateClass($class)->instance = null;
        $actual = $class::instance();

        $this->assertInstanceOf($class, $actual);
        $this->assertSame($actual, $class::instance());
    }
}
