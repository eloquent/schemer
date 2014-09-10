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

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

/**
 * Represents an individual property dependency constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.4.5
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.4.5.2.2
 */
class PropertyDependencyConstraint implements ConstraintInterface
{
    /**
     * Construct a new property dependency constraint.
     *
     * @param string        $property            The property.
     * @param array<string> $dependentProperties The dependent properties.
     */
    public function __construct($property, array $dependentProperties)
    {
        $this->property = $property;
        $this->dependentProperties = $dependentProperties;
    }

    /**
     * Get the property.
     *
     * @return string The property.
     */
    public function property()
    {
        return $this->property;
    }

    /**
     * Get the dependent properties.
     *
     * @return array<string> The dependent properties.
     */
    public function dependentProperties()
    {
        return $this->dependentProperties;
    }

    /**
     * Accept the supplied visitor.
     *
     * @param ConstraintVisitorInterface $visitor The visitor to accept.
     *
     * @return mixed The result of visitation.
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitPropertyDependencyConstraint($this);
    }

    private $property;
    private $dependentProperties;
}
