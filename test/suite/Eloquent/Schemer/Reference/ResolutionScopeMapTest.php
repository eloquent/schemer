<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference;

use Eloquent\Schemer\Pointer\PointerFactory;
use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Uri\UriFactory;
use FilesystemIterator;
use PHPUnit_Framework_TestCase;

class ResolutionScopeMapTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->reader = new Reader;
        $this->fixturePath = sprintf(
            '%s/../../../../fixture/reference/scope-map',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->pointerFactory = new PointerFactory;
        $this->uriFactory = new UriFactory(array());
    }

    public function pointerByUriData()
    {
        $testNames = $this->reader->readPath(
            sprintf('%s/pointerByUri.json', $this->fixturePath)
        )->tests->keys();

        return array_combine(
            $testNames,
            array_map(
                function ($testName) {
                    return array($testName);
                },
                $testNames
            )
        );
    }

    /**
     * @dataProvider pointerByUriData
     */
    public function testPointerByUri($testName)
    {
        $fixture = $this->reader->readPath(
            sprintf('%s/pointerByUri.json', $this->fixturePath)
        );

        $map = array();
        foreach ($fixture->map as $pointer => $uri) {
            $map[] = array(
                $this->pointerFactory->create($pointer),
                $this->uriFactory->create($uri->value()),
            );
        }
        $map = new ResolutionScopeMap($map);

        $test = $fixture->tests->$testName;
        $uri = $this->uriFactory->create($test->uri->value());
        $pointer = $map->pointerByUri($uri);

        if (null === $test->pointer->value()) {
            $this->assertNull($pointer);
        } else {
            $this->assertNotNull($pointer);
            $this->assertSame($test->pointer->value(), $pointer->string());
        }
    }
}
