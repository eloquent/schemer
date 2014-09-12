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

class PropertyDependencyConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->property = 'propertyA';
        $this->dependentProperties = ['propertyB', 'propertyC'];
        $this->constraint = new PropertyDependencyConstraint($this->property, $this->dependentProperties);
    }

    public function testConstructor()
    {
        $this->assertSame($this->property, $this->constraint->property());
        $this->assertSame($this->dependentProperties, $this->constraint->dependentProperties());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitPropertyDependencyConstraint($this->constraint);
    }
}
