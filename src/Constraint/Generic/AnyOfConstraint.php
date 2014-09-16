<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Generic;

use Eloquent\Schemer\Constraint\AbstractSchemaContainerConstraint;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents an 'anyOf' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.5.4
 */
class AnyOfConstraint extends AbstractSchemaContainerConstraint
{
    /**
     * Accept the supplied visitor.
     *
     * @param ConstraintVisitorInterface $visitor The visitor to accept.
     *
     * @return mixed The result of visitation.
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitAnyOfConstraint($this);
    }
}