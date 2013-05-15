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

use Eloquent\Equality\Comparator;
use Eloquent\Schemer\Constraint\Factory\SchemaFactory;
use Eloquent\Schemer\Reader\Reader;
use FilesystemIterator;
use PHPUnit_Framework_TestCase;

class DefaultValueTransformTest extends PHPUnit_Framework_TestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->reader = new Reader;
        $this->fixturePath = sprintf(
            '%s/../../../../fixture/default',
            __DIR__
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->schemaFactory = new SchemaFactory;
        $this->validator = new DefaultingConstraintValidator;
        $this->comparator = new Comparator;
    }

    public function transformData()
    {
        $iterator = new FilesystemIterator(
            $this->fixturePath,
            FilesystemIterator::SKIP_DOTS
        );

        $data = array();
        foreach ($iterator as $file) {
            $fixture = $this->reader->readPath(strval($file));
            $category = $file->getFilename();

            foreach ($fixture->tests as $testName => $test) {
                $data[sprintf('%s / %s', $category, $testName)] =
                    array($category, $testName);
            }
        }

        return $data;
    }

    /**
     * @dataProvider transformData
     */
    public function testTransform($category, $testName)
    {
        $fixture = $this->reader->readPath(
            sprintf('%s/%s', $this->fixturePath, $category)
        );
        $test = $fixture->tests->$testName;
        $actual = clone $test->value;
        $this->validator->validateAndApplyDefaults(
            $this->schemaFactory->create($fixture->schema),
            $actual
        );
        $expected = $test->expected;

        $this->assertEquals($expected, $actual);
        $this->assertTrue($this->comparator->equals($expected, $actual));
    }
}
