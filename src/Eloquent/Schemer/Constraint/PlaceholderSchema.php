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

class PlaceholderSchema implements SchemaInterface
{
    public function __construct()
    {
        $this->setInnerSchema(new Schema);
    }

    /**
     * @param Schema $schema
     */
    public function setInnerSchema(Schema $schema)
    {
        return $this->innerSchema = $schema;
    }

    /**
     * @return Schema
     */
    public function innerSchema()
    {
        return $this->innerSchema;
    }

    /**
     * @param Visitor\ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(Visitor\ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitPlaceholderSchema($this);
    }

    private $innerSchema;
}
