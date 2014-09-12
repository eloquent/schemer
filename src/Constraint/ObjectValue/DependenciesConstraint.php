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
 * Represents a 'dependencies' constraint.
 *
 * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.4.5
 */
class DependenciesConstraint implements ConstraintInterface
{
    /**
     * Construct a new dependencies constraint.
     *
     * @param array<string,SchemaInterface|array<string>> $dependencies The dependencies.
     */
    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * Get the dependencies.
     *
     * @return array<string,SchemaInterface|array<string>> The dependencies.
     */
    public function dependencies()
    {
        return $this->dependencies;
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
        return $visitor->visitDependenciesConstraint($this);
    }

    private $dependencies;
}
