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

class SchemaDependencyConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->property = 'propertyA';
        $this->schema = new Schema;
        $this->constraint = new SchemaDependencyConstraint($this->property, $this->schema);
    }

    public function testConstructor()
    {
        $this->assertSame($this->property, $this->constraint->property());
        $this->assertSame($this->schema, $this->constraint->schema());
    }

    public function testConstructorDefaults()
    {
        $this->constraint = new SchemaDependencyConstraint($this->property);

        $this->assertSame(Schema::createEmpty(), $this->constraint->schema());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitSchemaDependencyConstraint($this->constraint);
    }
}
