<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Resolver;

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Reader\Reader;
use PHPUnit_Framework_TestCase;
use Zend\Uri\File as FileUri;

class ReferenceResolverTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->fixturePath = sprintf(
            '%s/../../../../fixture/reference',
            __DIR__
        );
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

    public function testResolveRelative()
    {
        $path = sprintf('%s/complete.a.json', $this->fixturePath);
        $resolver = $this->factory->create($this->pathUriFixture($path));
        $actual = $resolver->resolve($this->reader->readPath($path));
        $expected = $this->reader->readPath(
            sprintf('%s/complete.expected.json', $this->fixturePath)
        );

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }
}
