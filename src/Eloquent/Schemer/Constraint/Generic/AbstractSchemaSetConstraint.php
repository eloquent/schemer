<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Generic;

use Eloquent\Schemer\Constraint\SchemaInterface;

abstract class AbstractSchemaSetConstraint implements SchemaSetConstraintInterface
{
    /**
     * @param array<SchemaInterface> $schemas
     */
    public function __construct(array $schemas)
    {
        $this->schemas = $schemas;
    }

    /**
     * @return array<SchemaInterface>
     */
    public function schemas()
    {
        return $this->schemas;
    }

    private $schemas;
}
