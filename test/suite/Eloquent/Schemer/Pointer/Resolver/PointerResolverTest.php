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
use FilesystemIterator;
use PHPUnit_Framework_TestCase;

class PointerResolverTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->reader = new Reader;
        $this->fixturePath = sprintf(
            '%s/../../../../../fixture/pointer',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->resolver = new PointerResolver;
        $this->pointerFactory = new PointerFactory;
        $this->comparator = new Comparator;
    }

    public function resolverData()
    {
        $iterator = new FilesystemIterator(
            $this->fixturePath,
            FilesystemIterator::SKIP_DOTS
        );

        $data = array();
        foreach ($iterator as $file) {
            $fixture = $this->reader->readPath(strval($file));
            $category = $file->getBaseName('.json');

            foreach ($fixture->get('tests') as $testName => $test) {
                $data[sprintf('%s / %s ', $category, $testName)] =
                    array($category, $testName);
            }

        }

        return $data;
    }

    /**
     * @dataProvider resolverData
     */
    public function testResolver($category, $testName)
    {
        $fixture = $this->reader->readPath(
            sprintf('%s/%s.json', $this->fixturePath, $category)
        );
        $test = $fixture->get('tests')->get($testName);
        $actual = $this->resolver->resolve(
            $this->pointerFactory->create(
                $test->get('pointer')->value()
            ),
            $fixture->get('document')
        );
        $expected = $test->get('expected');

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }
}
