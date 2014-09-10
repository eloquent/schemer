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

use PHPUnit_Framework_TestCase;
use Phake;

class TypeConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->types = array('string', 'number');
        $this->constraint = new TypeConstraint($this->types);
    }

    public function testConstructor()
    {
        $this->assertSame($this->types, $this->constraint->types());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitTypeConstraint($this->constraint);
    }
}
