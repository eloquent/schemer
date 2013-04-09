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
use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Value\ValueType;
use PHPUnit_Framework_TestCase;

class ConstraintValidatorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->validator = new ConstraintValidator;
        $this->reader = new Reader;
        $this->schema = new Schema(
            array(
                new TypeConstraint(ValueType::STRING_TYPE()),
            )
        );

        $this->comparator = new Comparator;
    }

    public function testValidateSchema()
    {
        $value = $this->reader->readString('"foo"');
        $result = $this->validator->validate($this->schema, $value);
        $expected = new Result\ValidationResult;

        $this->assertEquals($expected, $result);
        $this->assertTrue($this->comparator->equals($expected, $result));
        $this->assertTrue($result->isValid());
    }

    public function testValidateSchemaFailure()
    {
        $value = $this->reader->readString('null');
        $result = $this->validator->validate($this->schema, $value);
        $expected = new Result\ValidationResult(
            array(
                new Result\ValidationIssue(
                    $this->schema->constraints()[0],
                    $value,
                    new Pointer
                )
            )
        );

        $this->assertEquals($expected, $result);
        $this->assertTrue($this->comparator->equals($expected, $result));
        $this->assertFalse($result->isValid());
    }
}
