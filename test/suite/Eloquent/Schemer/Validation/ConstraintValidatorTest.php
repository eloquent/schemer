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
use Eloquent\Schemer\Constraint\Reader\SchemaReader;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Value\ValueInterface;
use PHPUnit_Framework_TestCase;

class ConstraintValidatorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->validator = new ConstraintValidator;

        $this->comparator = new Comparator;
    }

    public function validateSchemaData()
    {
        $data = array();

        $schemaReader = new SchemaReader;
        $reader = new Reader;

        $schema = $schemaReader->readString('{"type": "string"}');
        $constraints = $schema->constraints();

        $value = $reader->readString('"foo"');
        $expected = new Result\ValidationResult;
        $data['Successful validation'] = array($schema, $value, $expected);

        $value = $reader->readString('null');
        $expected = new Result\ValidationResult(
            array(
                new Result\ValidationIssue(
                    $constraints[0],
                    $value,
                    new Pointer
                )
            )
        );
        $data['Failed validation'] = array($schema, $value, $expected);

        return $data;
    }

    /**
     * @dataProvider validateSchemaData
     */
    public function testValidateSchema(
        Schema $schema,
        ValueInterface $value,
        Result\ValidationResult $expected
    ) {
        $result = $this->validator->validate($schema, $value);

        $this->assertEquals($expected, $result);
        $this->assertTrue($this->comparator->equals($expected, $result));
    }
}
