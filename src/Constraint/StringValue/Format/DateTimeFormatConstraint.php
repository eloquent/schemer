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

use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents a 'date-time' format constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.7.3.1
 */
class DateTimeFormatConstraint implements FormatConstraintInterface
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
        return $visitor->visitDateTimeFormatConstraint($this);
    }
}
