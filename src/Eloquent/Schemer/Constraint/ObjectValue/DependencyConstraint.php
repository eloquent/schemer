<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ObjectValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\SchemaInterface;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

class DependencyConstraint implements ConstraintInterface
{
    /**
     * @param string          $property
     * @param SchemaInterface $schema
     */
    public function __construct($property, SchemaInterface $schema)
    {
        $this->property = $property;
        $this->schema = $schema;
    }

    /**
     * @return string
     */
    public function property()
    {
        return $this->property;
    }

    /**
     * @return SchemaInterface
     */
    public function schema()
    {
        return $this->schema;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitDependencyConstraint($this);
    }

    private $property;
    private $schema;
}
