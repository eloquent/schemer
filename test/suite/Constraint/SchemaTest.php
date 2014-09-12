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
        $this->constraints = [$this->constraintA, $this->constraintB];
        $this->defaultValue = 'defaultValue';
        $this->title = 'title';
        $this->description = 'description';
        $this->schema = new Schema($this->constraints, $this->defaultValue, $this->title, $this->description);

        $this->constraintC = Phake::mock('Eloquent\Schemer\Constraint\ConstraintInterface');
    }

    public function testConstructor()
    {
        $this->assertSame($this->constraints, $this->schema->constraints());
        $this->assertSame($this->defaultValue, $this->schema->defaultValue());
        $this->assertSame($this->title, $this->schema->title());
        $this->assertSame($this->description, $this->schema->description());
    }

    public function testConstructorDefaults()
    {
        $this->schema = new Schema;

        $this->assertSame([], $this->schema->constraints());
        $this->assertNull($this->schema->defaultValue());
        $this->assertNull($this->schema->title());
        $this->assertNull($this->schema->description());
    }

    public function testSetConstraints()
    {
        $this->constraints = [$this->constraintA, $this->constraintB];
        $this->schema->setConstraints($this->constraints);

        $this->assertSame($this->constraints, $this->schema->constraints());
    }

    public function testAddConstraint()
    {
        $this->schema->addConstraint($this->constraintC);

        $this->assertSame([$this->constraintA, $this->constraintB, $this->constraintC], $this->schema->constraints());
    }

    public function testRemoveConstraint()
    {
        $this->schema->addConstraint($this->constraintB);

        $this->assertTrue($this->schema->removeConstraint($this->constraintA));
        $this->assertTrue($this->schema->removeConstraint($this->constraintB));
        $this->assertFalse($this->schema->removeConstraint($this->constraintC));
        $this->assertSame([$this->constraintB], $this->schema->constraints());
    }

    public function testSetDefaultValue()
    {
        $this->schema->setDefaultValue(null);

        $this->assertNull($this->schema->defaultValue());

        $this->schema->setDefaultValue($this->defaultValue);

        $this->assertSame($this->defaultValue, $this->schema->defaultValue());
    }

    public function testSetTitle()
    {
        $this->schema->setTitle(null);

        $this->assertNull($this->schema->title());

        $this->schema->setTitle($this->title);

        $this->assertSame($this->title, $this->schema->title());
    }

    public function testSetDescription()
    {
        $this->schema->setDescription(null);

        $this->assertNull($this->schema->description());

        $this->schema->setDescription($this->description);

        $this->assertSame($this->description, $this->schema->description());
    }

    public function testIsEmpty()
    {
        $emptySchema = new Schema;

        $this->assertFalse($this->schema->isEmpty());
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
        $visitor = Phake::mock('Eloquent\Schemer\Constraint\ConstraintVisitorInterface');
        $this->schema->accept($visitor);

        Phake::verify($visitor)->visitSchema($this->schema);
    }
}
