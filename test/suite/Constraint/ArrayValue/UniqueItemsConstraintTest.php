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

use Phake;
use PHPUnit_Framework_TestCase;

class UniqueItemsConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->constraint = new UniqueItemsConstraint(false);
    }

    public function testConstructor()
    {
        $this->assertFalse($this->constraint->isUnique());
    }

    public function testConstructorDefaults()
    {
        $this->constraint = new UniqueItemsConstraint;

        $this->assertTrue($this->constraint->isUnique());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitUniqueItemsConstraint($this->constraint);
    }
}
