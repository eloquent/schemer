<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Constraint;

use Eloquent\Schemer\Constraint\SchemaInterface;

/**
 * The interface implemented by schema specification generators.
 */
interface SchemaSpecificationGeneratorInterface
{
    /**
     * Transform the supplied schema into a schema specification.
     *
     * @param SchemaInterface $schema The schema.
     *
     * @return mixed The schema specification.
     */
    public function schemaToSpecification(SchemaInterface $schema);
}
