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

use Eloquent\Schemer\Constraint\Reader\SchemaReader;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Validation\Result\IssueRenderer;
use PHPUnit_Framework_TestCase;

class ConstraintValidatorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->validator = new ConstraintValidator;

        $this->schemaReader = new SchemaReader;
        $this->reader = new Reader;
        $this->renderer = new IssueRenderer;
    }

    public function validateSchemaData()
    {
        return array(
            'Successful validation' => array(
                '{"type": "string"}',
                '"foo"',
                array(),
            ),

            'Failed validation' => array(
                '{"type": "string"}',
                'null',
                array(
                    "Validation failed for value at '#': The value must be of type 'string'.",
                ),
            ),
        );
    }

    /**
     * @dataProvider validateSchemaData
     */
    public function testValidateSchema($schema, $value, array $expected)
    {
        $schema = $this->schemaReader->readString('{"type": "string"}');
        $value = $this->reader->readString($value);
        $result = $this->validator->validate($schema, $value);
        $actual = array();
        foreach ($result->issues() as $issue) {
            $actual[] = $this->renderer->render($issue);
        }

        $this->assertSame($expected, $actual);
    }
}
