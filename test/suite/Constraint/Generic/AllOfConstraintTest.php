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
use Phake;
use PHPUnit_Framework_TestCase;

class AllOfConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->schemas = array(new Schema, new Schema);
        $this->constraint = new AllOfConstraint($this->schemas);
    }

    public function testConstructor()
    {
        $this->assertSame($this->schemas, $this->constraint->schemas());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitAllOfConstraint($this->constraint);
    }
}
