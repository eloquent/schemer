<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Factory;

use Eloquent\Schemer\Reader\SwitchingScopeResolvingReader;
use PHPUnit_Framework_TestCase;
use Zend\Uri\File as FileUri;

class SchemaFactoryTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->fixturePath = sprintf(
            '%s/../../../../../fixture/schema',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->factory = new SchemaFactory;
        $this->reader = new SwitchingScopeResolvingReader;
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

    public function testRecursiveSchemaCreation()
    {
        $path = sprintf('%s/recursive-inline.json', $this->fixturePath);
        $value = $this->reader->readPath($path);
        $schema = $this->factory->create($value);
        $constraints = $schema->constraints();
        $propertiesConstraint = $constraints[1];
        $additionalSchema = $propertiesConstraint->additionalSchema();
        $nestedConstraints = $additionalSchema->constraints();
        $nestedPropertiesConstraint = $nestedConstraints[1];
        $nestedAdditionalSchema = $nestedPropertiesConstraint->additionalSchema();

        $this->assertSame($additionalSchema, $nestedAdditionalSchema);
    }
}
