<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Transform;

use Eloquent\Schemer\Constraint\PlaceholderSchema;

class PlaceholderUnwrapTransform extends AbstractConstraintTransform
{
    /**
     * @param PlaceholderSchema $schame
     *
     * @return PlaceholderSchema
     */
    public function visitPlaceholderSchema(PlaceholderSchema $schema)
    {
        return $schema->innerSchema()->accept($this);
    }
}
