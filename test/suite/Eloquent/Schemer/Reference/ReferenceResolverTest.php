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

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Reader\Reader;
use PHPUnit_Framework_TestCase;
use Zend\Uri\File as FileUri;

class ReferenceResolverTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->fixturePath = sprintf(
            '%s/../../../../fixture/reference',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->factory = new ReferenceResolverFactory;
        $this->reader = new Reader;
        $this->comparator = new Comparator;
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

    public function resolverData()
    {
        $data = array();
        foreach (scandir($this->fixturePath) as $item) {
            if (
                '.' === $item ||
                '..' === $item ||
                !is_dir(sprintf('%s/%s', $this->fixturePath, $item)) ||
                !is_file(sprintf('%s/%s/expected.json', $this->fixturePath, $item))
            ) {
                continue;
            }

            $data[] = array($item);
        }

        return $data;
    }

    /**
     * @dataProvider resolverData
     */
    public function testResolver($testName)
    {
        $path = sprintf('%s/%s/document.json', $this->fixturePath, $testName);
        $resolver = $this->factory->create($this->pathUriFixture($path));
        $actual = $resolver->transform($this->reader->readPath($path));
        $expected = $this->reader->readPath(
            sprintf('%s/%s/expected.json', $this->fixturePath, $testName)
        );

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }

    public function testResolveResolvableRecursive()
    {
        $path = sprintf(
            '%s/recursive/resolvable-inline.json',
            $this->fixturePath
        );
        $resolver = $this->factory->create($this->pathUriFixture($path));
        $value = $resolver->transform($this->reader->readPath($path));

        $this->assertSame('splat', $value->a->foo->bar->foo->doom->value());
    }
}
