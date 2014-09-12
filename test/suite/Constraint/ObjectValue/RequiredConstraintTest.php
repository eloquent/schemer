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

use Phake;
use PHPUnit_Framework_TestCase;

class RequiredConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->properties = ['propertyA', 'propertyB'];
        $this->constraint = new RequiredConstraint($this->properties);
    }

    public function testConstructor()
    {
        $this->assertSame($this->properties, $this->constraint->properties());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitRequiredConstraint($this->constraint);
    }
}
