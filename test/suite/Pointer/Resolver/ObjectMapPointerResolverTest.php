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
    }

    public function testResolve()
    {
        $value = (object) [
            'propertyA' => (object) [
                'propertyAA' => [
                    'valueAAA',
                    'valueAAB',
                    (object) [],
                ],
            ],
            'propertyB' => 'valueB',
        ];

        $this->assertSame([$value, true], $this->resolver->resolve($value, Pointer::create('')));
        $this->assertSame([$value->propertyA, true], $this->resolver->resolve($value, Pointer::create('/propertyA')));
        $this->assertSame(
            [$value->propertyA->propertyAA, true],
            $this->resolver->resolve($value, Pointer::create('/propertyA/propertyAA'))
        );
        $this->assertSame(
            [$value->propertyA->propertyAA[0], true],
            $this->resolver->resolve($value, Pointer::create('/propertyA/propertyAA/0'))
        );
        $this->assertSame(
            [$value->propertyA->propertyAA[1], true],
            $this->resolver->resolve($value, Pointer::create('/propertyA/propertyAA/1'))
        );
        $this->assertSame(
            [$value->propertyA->propertyAA[2], true],
            $this->resolver->resolve($value, Pointer::create('/propertyA/propertyAA/2'))
        );
        $this->assertSame([$value->propertyB, true], $this->resolver->resolve($value, Pointer::create('/propertyB')));
        $this->assertSame([null, false], $this->resolver->resolve($value, Pointer::create('/')));
        $this->assertSame([null, false], $this->resolver->resolve($value, Pointer::create('/PROPERTYA')));
        $this->assertSame([null, false], $this->resolver->resolve($value, Pointer::create('/propertyC')));
        $this->assertSame([null, false], $this->resolver->resolve($value, Pointer::create('/propertyB/propertyC')));
        $this->assertSame([null, false], $this->resolver->resolve($value, Pointer::create('/propertyAA')));
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
