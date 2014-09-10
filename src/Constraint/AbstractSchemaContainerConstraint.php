<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint;

/**
 * An abstract base class for implementing schema container constraints.
 */
abstract class AbstractSchemaContainerConstraint implements
    SchemaContainerConstraintInterface
{
    /**
     * Construct a new schema container constraint.
     *
     * @param array<SchemaInterface> $schemas The schemas.
     */
    public function __construct(array $schemas)
    {
        $this->schemas = $schemas;
    }

    /**
     * Get the schemas.
     *
     * @return array<SchemaInterface> The schemas.
     */
    public function schemas()
    {
        return $this->schemas;
    }

    private $schemas;
}
