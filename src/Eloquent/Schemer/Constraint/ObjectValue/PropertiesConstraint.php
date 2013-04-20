<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\ObjectValue;

use Eloquent\Schemer\Constraint\ConstraintInterface;
use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Constraint\Visitor\ConstraintVisitorInterface;

class PropertiesConstraint implements ConstraintInterface
{
    /**
     * @param array<string,Schema> $schemas
     * @param array<string,Schema> $patternSchemas
     * @param Schema               $additionalSchema
     */
    public function __construct(
        array $schemas,
        array $patternSchemas,
        Schema $additionalSchema
    ) {
        $this->schemas = $schemas;
        $this->patternSchemas = $patternSchemas;
        $this->additionalSchema = $additionalSchema;
    }

    /**
     * @return array<string,Schema>
     */
    public function schemas()
    {
        return $this->schemas;
    }

    /**
     * @return array<string,Schema>
     */
    public function patternSchemas()
    {
        return $this->patternSchemas;
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
        return $visitor->visitPropertiesConstraint($this);
    }

    private $schemas;
    private $patternSchemas;
    private $additionalSchema;
}
