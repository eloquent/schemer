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

use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Pointer\PointerFactory;
use Eloquent\Schemer\Uri\UriFactory;
use FilesystemIterator;
use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Zend\Uri\File as FileUri;

class ResolutionScopeMapperTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->fixturePath = sprintf(
            '%s/../../../../fixture/reference/scope-mapper',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->reader = new Reader;
        $this->pointerFactory = new PointerFactory;
        $this->uriFactory = new UriFactory;
    }

    protected function pathUriFixture($path)
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $uri = FileUri::fromWindowsPath($path);
        } else {
            $uri = FileUri::fromUnixPath($path);
        }

        return $uri;
    }

    public function mapperData()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->fixturePath,
                FilesystemIterator::SKIP_DOTS
            )
        );

        $data = array();
        foreach ($iterator as $file) {
            $data[$file->getFilename()] = array($file->getFilename());
        }

        return $data;
    }

    /**
     * @dataProvider mapperData
     */
    public function testMapper($testName)
    {
        $path = sprintf('%s/%s', $this->fixturePath, $testName);
        $mapper = new ResolutionScopeMapper($this->uriFactory->create('#'));
        $fixture = $this->reader->readPath($path);
        $expected = get_object_vars($fixture->expected->value());
        $actual = array();
        foreach ($mapper->create($fixture->document)->map() as $uri => $pointer) {
            $actual[sprintf('#%s', $pointer->string())] = $uri;
        }

        $this->assertSame($expected, $actual);
    }
}
