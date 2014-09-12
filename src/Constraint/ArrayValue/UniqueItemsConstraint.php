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

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;

/**
 * Represents a 'uniqueItems' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.3.4
 */
class UniqueItemsConstraint implements ConstraintInterface
{
    /**
     * Construct a new unique items constraint.
     *
     * @param boolean|null $isUnique True if the values must be unique.
     */
    public function __construct($isUnique = null)
    {
        if (null === $isUnique) {
            $isUnique = true;
        }

        $this->isUnique = $isUnique;
    }

    /**
     * Returns true if the values must be unique.
     *
     * @return boolean True if the values must be unique.
     */
    public function isUnique()
    {
        return $this->isUnique;
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
        return $visitor->visitUniqueItemsConstraint($this);
    }

    private $isUnique;
}
