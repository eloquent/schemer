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
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents an individual element of a 'required' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.4.3
 */
class RequiredConstraint implements ConstraintInterface
{
    /**
     * Construct a new required constraint.
     *
     * @param array<string> $properties The properties.
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * Get the properties.
     *
     * @return array<string> The properties.
     */
    public function properties()
    {
        return $this->properties;
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
        return $visitor->visitRequiredConstraint($this);
    }

    private $properties;
}
