<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint;

interface ConstraintVisitorInterface
{
    /**
     * @param Schema $constraint
     *
     * @return mixed
     */
    public function visitSchema(Schema $constraint);

    // generic constraints

    /**
     * @param Generic\TypeConstraint $constraint
     *
     * @return mixed
     */
    public function visitTypeConstraint(Generic\TypeConstraint $constraint);
}
