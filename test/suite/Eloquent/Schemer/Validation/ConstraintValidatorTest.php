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

use Eloquent\Schemer\Constraint\Factory\SchemaFactory;
use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Validation\Result\IssueRenderer;
use FilesystemIterator;
use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ConstraintValidatorTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->reader = new Reader;
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

        $this->schemaFactory = new SchemaFactory;
        $this->renderer = new IssueRenderer;
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

            foreach ($fixture->get('tests') as $testName => $test) {
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
        $fixture = $this->reader->readPath(
            sprintf('%s/%s/%s', $this->fixturePath, $constraint, $category)
        );
        $test = $fixture->get('tests')->get($testName);
        $result = $this->validator->validate(
            $this->schemaFactory->create($fixture->get('schema')),
            $test->get('value')
        );

        $this->assertSame(
            $test->get('expected')->rawValue(),
            $this->renderer->renderMany($result->issues())
        );
    }
}
