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
use Eloquent\Schemer\Uri\UriFactory;
use PHPUnit_Framework_TestCase;

class ReferenceResolverTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->schemataPath = sprintf(
            '%s/../../../../../resources/schemata',
            __DIR__
        );
        $this->fixturePath = sprintf(
            '%s/../../../../fixture/reference/resolver',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->factory = new SwitchingScopeReferenceResolverFactory;
        $this->uriFactory = new UriFactory;
        $this->reader = new Reader;
        $this->comparator = new Comparator;
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

            $data[$item] = array($item);
        }

        return $data;
    }

    /**
     * @dataProvider resolverData
     */
    public function testResolver($testName)
    {
        $path = sprintf('%s/%s/document.json', $this->fixturePath, $testName);
        $resolver = $this->factory->create($this->uriFactory->fromPath($path));
        $actual = $resolver->transform($this->reader->readPath($path));
        $expected = $this->reader->readPath(
            sprintf('%s/%s/expected.json', $this->fixturePath, $testName)
        );

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }

    public function resolvableRecursiveData()
    {
        return array(
            'resolvable-inline.json' => array('resolvable-inline.json'),
            'resolvable-external.json' => array('resolvable-external.json'),
            'resolvable-external-switching-scope.json' => array('resolvable-external-switching-scope.json'),
        );
    }

    /**
     * @dataProvider resolvableRecursiveData
     */
    public function testResolveResolvableRecursive($test)
    {
        $path = sprintf('%s/recursive/%s', $this->fixturePath, $test);
        $resolver = $this->factory->create($this->uriFactory->fromPath($path));
        $value = $resolver->transform($this->reader->readPath($path));

        $this->assertSame('splat', $value->a->foo->bar->foo->doom->value());
    }

    public function testResolveResolvableMetaSchema()
    {
        $path = sprintf('%s/meta-schema.json', $this->schemataPath);
        $resolver = $this->factory->create($this->uriFactory->fromPath($path));
        $value = $resolver->transform($this->reader->readPath($path));

        $this->assertSame(
            'Schemer meta-schema',
            $value->properties->allOf->items->title->value()
        );
    }

    public function testResolveResolvableJsonSchemaMetaSchema()
    {
        $path = sprintf('%s/json-schema-meta-schema.json', $this->schemataPath);
        $resolver = $this->factory->create($this->uriFactory->fromPath($path));
        $value = $resolver->transform($this->reader->readPath($path));

        $this->assertSame(
            'Core schema meta-schema',
            $value->properties->allOf->items->description->value()
        );
    }

    public function testResolveResolvableRecursiveFucked()
    {
        $path = sprintf(
            '%s/recursive/resolvable-fucked.json',
            $this->fixturePath
        );
        $resolver = $this->factory->create($this->uriFactory->fromPath($path));
        $value = $resolver->transform($this->reader->readPath($path));

        $this->assertTrue($value->foo->has('qux'));
    }

    public function testResolveUnresolvableRecursiveInline()
    {
        $path = sprintf(
            '%s/recursive/unresolvable-inline.json',
            $this->fixturePath
        );
        $resolver = $this->factory->create($this->uriFactory->fromPath($path));
        $value = $resolver->transform($this->reader->readPath($path));

        $this->assertNull($value->a->value());
        $this->assertNull($value->b->value());
        $this->assertSame($value->a, $value->b);
    }

    public function testResolveUnresolvableRecursiveExternal()
    {
        $path = sprintf(
            '%s/recursive/unresolvable-external.json',
            $this->fixturePath
        );
        $resolver = $this->factory->create($this->uriFactory->fromPath($path));
        $value = $resolver->transform($this->reader->readPath($path));

        $this->assertNull($value->a->value());
        $this->assertFalse($value->has('b'));
    }
}
