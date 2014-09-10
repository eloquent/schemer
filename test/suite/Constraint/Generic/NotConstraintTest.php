<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Generic;

use Eloquent\Schemer\Constraint\Schema;
use PHPUnit_Framework_TestCase;
use Phake;

class NotConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->schema = new Schema;
        $this->constraint = new NotConstraint($this->schema);
    }

    public function testConstructor()
    {
        $this->assertSame($this->schema, $this->constraint->schema());
    }

    public function testConstructorDefaults()
    {
        $this->constraint = new NotConstraint;

        $this->assertSame(Schema::createEmpty(), $this->constraint->schema());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitNotConstraint($this->constraint);
    }
}
