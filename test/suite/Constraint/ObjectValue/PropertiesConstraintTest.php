<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ObjectValue;

use Eloquent\Schemer\Constraint\Schema;
use PHPUnit_Framework_TestCase;
use Phake;

class PropertiesConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->schemas = [new Schema, new Schema];
        $this->patternSchemas = [new Schema, new Schema];
        $this->additionalSchema = new Schema;
        $this->constraint = new PropertiesConstraint($this->schemas, $this->patternSchemas, $this->additionalSchema);
    }

    public function testConstructor()
    {
        $this->assertSame($this->schemas, $this->constraint->schemas());
        $this->assertSame($this->patternSchemas, $this->constraint->patternSchemas());
        $this->assertSame($this->additionalSchema, $this->constraint->additionalSchema());
    }

    public function testConstructorDefaults()
    {
        $this->constraint = new PropertiesConstraint;

        $this->assertNull($this->constraint->schemas());
        $this->assertNull($this->constraint->patternSchemas());
        $this->assertNull($this->constraint->additionalSchema());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitPropertiesConstraint($this->constraint);
    }
}
