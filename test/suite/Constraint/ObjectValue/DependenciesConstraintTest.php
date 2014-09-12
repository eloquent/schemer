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

class DependenciesConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->dependencies = [
            'propertyA' => new Schema,
            'propertyB' => ['propertyC', 'propertyD'],
        ];
        $this->constraint = new DependenciesConstraint($this->dependencies);
    }

    public function testConstructor()
    {
        $this->assertSame($this->dependencies, $this->constraint->dependencies());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitDependenciesConstraint($this->constraint);
    }
}
