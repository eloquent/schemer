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

    // generic constraints =====================================================

    /**
     * @param Generic\EnumConstraint $constraint
     *
     * @return mixed
     */
    public function visitEnumConstraint(Generic\EnumConstraint $constraint);

    /**
     * @param Generic\TypeConstraint $constraint
     *
     * @return mixed
     */
    public function visitTypeConstraint(Generic\TypeConstraint $constraint);

    /**
     * @param Generic\AllOfConstraint $constraint
     *
     * @return mixed
     */
    public function visitAllOfConstraint(Generic\AllOfConstraint $constraint);

    /**
     * @param Generic\AnyOfConstraint $constraint
     *
     * @return mixed
     */
    public function visitAnyOfConstraint(Generic\AnyOfConstraint $constraint);

    /**
     * @param Generic\OneOfConstraint $constraint
     *
     * @return mixed
     */
    public function visitOneOfConstraint(Generic\OneOfConstraint $constraint);

    /**
     * @param Generic\NotConstraint $constraint
     *
     * @return mixed
     */
    public function visitNotConstraint(Generic\NotConstraint $constraint);

    // object constraints ======================================================

    /**
     * @param ObjectValue\PropertyConstraint $constraint
     *
     * @return mixed
     */
    public function visitPropertyConstraint(ObjectValue\PropertyConstraint $constraint);
}
