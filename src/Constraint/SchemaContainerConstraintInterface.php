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
 * The interface implemented by constraints that contain a set of schemas.
 */
interface SchemaContainerConstraintInterface extends ConstraintInterface
{
    /**
     * Get the schemas.
     *
     * @return array<SchemaInterface> The schemas.
     */
    public function schemas();
}
