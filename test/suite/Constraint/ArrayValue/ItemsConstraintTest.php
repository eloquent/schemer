<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ArrayValue;

use Eloquent\Schemer\Constraint\Schema;
use Phake;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Schemer\Constraint\ArrayValue\ItemsConstraint
 * @covers \Eloquent\Schemer\Constraint\AbstractSchemaContainerConstraint
 */
class ItemsConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->schemas = array(new Schema, new Schema);
        $this->additionalSchema = new Schema;
        $this->constraint = new ItemsConstraint($this->schemas, $this->additionalSchema);
    }

    public function testConstructor()
    {
        $this->assertSame($this->schemas, $this->constraint->schemas());
        $this->assertSame($this->additionalSchema, $this->constraint->additionalSchema());
    }

    public function testConstructorDefaults()
    {
        $this->constraint = new ItemsConstraint;

        $this->assertSame(array(), $this->constraint->schemas());
        $this->assertSame(Schema::createEmpty(), $this->constraint->additionalSchema());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitItemsConstraint($this->constraint);
    }
}
