<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\StringValue\Format;

use Phake;
use PHPUnit_Framework_TestCase;

class UriFormatConstraintTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->constraint = new UriFormatConstraint;
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitUriFormatConstraint($this->constraint);
    }
}
