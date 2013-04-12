<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ArrayValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\ConstraintVisitorInterface;
use Eloquent\Schemer\Constraint\Schema;

class ItemsConstraint implements ConstraintInterface
{
    /**
     * @param array<integer,Schema> $schemas
     * @param Schema                $additionalSchema
     */
    public function __construct(
        array $schemas,
        Schema $additionalSchema
    ) {
        $this->schemas = $schemas;
        $this->additionalSchema = $additionalSchema;
    }

    /**
     * @return array<integer,Schema>
     */
    public function schemas()
    {
        return $this->schemas;
    }

    /**
     * @return Schema
     */
    public function additionalSchema()
    {
        return $this->additionalSchema;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitItemsConstraint($this);
    }

    private $schemas;
    private $additionalSchema;
}
