<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Validation;

use Eloquent\Schemer\Constraint\Factory\MetaSchemaFactory;
use Eloquent\Schemer\Constraint\Factory\SchemaFactory;
use Eloquent\Schemer\Reader\SwitchingScopeResolvingReader;
use Eloquent\Schemer\Validation\Result\IssueRenderer;
use FilesystemIterator;
use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Zend\Uri\File as FileUri;

class ConstraintValidatorTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->reader = new SwitchingScopeResolvingReader;
        $this->fixturePath = sprintf(
            '%s/../../../../fixture/constraint',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->validator = new ConstraintValidator;

        $this->metaSchemaFactory = new MetaSchemaFactory;
        $this->schemaFactory = new SchemaFactory(
            null,
            $this->metaSchemaFactory->create()
        );
        $this->renderer = new IssueRenderer;
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

    public function validateSchemaData()
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->fixturePath,
                FilesystemIterator::SKIP_DOTS
            )
        );

        $data = array();
        foreach ($iterator as $file) {
            $fixture = $this->reader->readPath(strval($file));
            $constraint = $file->getPathInfo()->getFilename();
            $category = $file->getFilename();

            foreach ($fixture->tests as $testName => $test) {
                $data[sprintf('%s / %s / %s', $constraint, $category, $testName)] =
                    array($constraint, $category, $testName);
            }

        }

        return $data;
    }

    /**
     * @dataProvider validateSchemaData
     */
    public function testValidateSchema($constraint, $category, $testName)
    {
        $path = sprintf('%s/%s/%s', $this->fixturePath, $constraint, $category);
        $fixture = $this->reader->readPath($path);
        $test = $fixture->tests->$testName;
        $result = $this->validator->validate(
            $this->schemaFactory->create($fixture->schema),
            $test->value
        );

        $this->assertSame(
            $test->expected->value(),
            $this->renderer->renderMany($result->issues())
        );
    }
}
