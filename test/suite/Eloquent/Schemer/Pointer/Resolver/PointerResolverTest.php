<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer\Resolver;

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Pointer\PointerFactory;
use Eloquent\Schemer\Reader\Reader;
use PHPUnit_Framework_TestCase;

class PointerResolverTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->resolver = new PointerResolver;
        $this->reader = new Reader;
        $this->pointerFactory = new PointerFactory;
        $this->comparator = new Comparator;
    }

    public function resolverData()
    {
        $documentA = <<<'EOD'
{
  "foo": ["bar", "baz"],
  "": 0,
  "a/b": 1,
  "c%d": 2,
  "e^f": 3,
  "g|h": 4,
  "i\\j": 5,
  "k\"l": 6,
  " ": 7,
  "m~n": 8
}
EOD;

        return array(
            'Spec example 1'  => array($documentA, '',       $documentA),
            'Spec example 2'  => array($documentA, '/foo',   '["bar", "baz"]'),
            'Spec example 3'  => array($documentA, '/foo/0', '"bar"'),
            'Spec example 4'  => array($documentA, '/',      '0'),
            'Spec example 5'  => array($documentA, '/a~1b',  '1'),
            'Spec example 6'  => array($documentA, '/c%d',   '2'),
            'Spec example 7'  => array($documentA, '/e^f',   '3'),
            'Spec example 8'  => array($documentA, '/g|h',   '4'),
            'Spec example 9'  => array($documentA, '/i\\j',  '5'),
            'Spec example 10' => array($documentA, '/k"l',   '6'),
            'Spec example 11' => array($documentA, '/ ',     '7'),
            'Spec example 12' => array($documentA, '/m~0n',  '8'),
        );
    }

    /**
     * @dataProvider resolverData
     */
    public function testResolver($document, $pointer, $expected)
    {
        $document = $this->reader->readString($document);
        $pointer = $this->pointerFactory->create($pointer);
        $expected = $this->reader->readString($expected);

        $this->assertEquals(
            $expected,
            $this->resolver->resolve($pointer, $document)
        );
        $this->assertTrue(
            $this->comparator->equals(
                $expected,
                $this->resolver->resolve($pointer, $document)
            )
        );
    }
}
