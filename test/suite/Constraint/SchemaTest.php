<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;
use Phake;

class SchemaTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->constraintA = Phake::mock('Eloquent\Schemer\Constraint\ConstraintInterface');
        $this->constraintB = Phake::mock('Eloquent\Schemer\Constraint\ConstraintInterface');
        $this->constraints = array($this->constraintA, $this->constraintB);
        $this->defaultValue = 'defaultValue';
        $this->title = 'title';
        $this->description = 'description';
        $this->constraint = new Schema($this->constraints, $this->defaultValue, $this->title, $this->description);
    }

    public function testConstructor()
    {
        $this->assertSame($this->constraints, $this->constraint->constraints());
        $this->assertSame($this->defaultValue, $this->constraint->defaultValue());
        $this->assertSame($this->title, $this->constraint->title());
        $this->assertSame($this->description, $this->constraint->description());
    }

    public function testConstructorDefaults()
    {
        $this->constraint = new Schema;

        $this->assertSame(array(), $this->constraint->constraints());
        $this->assertNull($this->constraint->defaultValue());
        $this->assertNull($this->constraint->title());
        $this->assertNull($this->constraint->description());
    }

    public function testIsEmpty()
    {
        $emptySchema = new Schema;

        $this->assertFalse($this->constraint->isEmpty());
        $this->assertTrue($emptySchema->isEmpty());
    }

    public function testCreateEmpty()
    {
        Liberator::liberateClass('Eloquent\Schemer\Constraint\Schema')->emptySchema = null;
        $actual = Schema::createEmpty();

        $this->assertEquals(new Schema, $actual);
        $this->assertSame($actual, Schema::createEmpty());
    }

    public function testAccept()
    {
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface');
        $this->constraint->accept($visitor);

        Phake::verify($visitor)->visitSchema($this->constraint);
    }
}
