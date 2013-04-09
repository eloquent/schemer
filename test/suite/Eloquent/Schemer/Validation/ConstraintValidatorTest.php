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
                '{"type": "object", "properties": {"foo": {"type": "string"}, "bar": {"type": "object"}}}',
                '{"foo": "bar"}',
                array(),
            ),

            'Failed validation' => array(
                '{"type": "object", "properties": {"foo": {"type": "string"}, "bar": {"type": "object"}}}',
                '{"foo": null, "bar": "baz"}',
                array(
                    "Validation failed for value at '/foo': The value must be of type 'string'.",
                    "Validation failed for value at '/bar': The value must be of type 'object'.",
                ),
            ),

            'Failed validation at document root' => array(
                '{"type": "string"}',
                'null',
                array(
                    "Validation failed for value at document root: The value must be of type 'string'.",
                ),
            ),
        );
    }

    /**
     * @dataProvider validateSchemaData
     */
    public function testValidateSchema($schema, $value, array $expected)
    {
        $schema = $this->schemaReader->readString($schema);
        $value = $this->reader->readString($value);
        $result = $this->validator->validate($schema, $value);
        $actual = array();
        foreach ($result->issues() as $issue) {
            $actual[] = $this->renderer->render($issue);
        }

        $this->assertSame($expected, $actual);
    }
}
