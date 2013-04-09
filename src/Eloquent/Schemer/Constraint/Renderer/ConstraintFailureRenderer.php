<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Renderer;

use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\Generic\TypeConstraint;
use Eloquent\Schemer\Constraint\Schema;

class ConstraintFailureRenderer implements ConstraintVisitorInterface
{
    /**
     * @param Schema $constraint
     *
     * @return string
     */
    public function visitSchema(Schema $constraint)
    {
        return 'The value did not match the defined schema.';
    }

    // generic constraints

    /**
     * @param TypeConstraint $constraint
     *
     * @return string
     */
    public function visitTypeConstraint(TypeConstraint $constraint)
    {
        return sprintf(
            "The value must be of type '%s'.", $constraint->type()->value()
        );
    }
}
