<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Factory;

use Eloquent\Schemer\Value\ConcreteValueInterface;

interface SchemaFactoryInterface
{
    /**
     * @param ConcreteValueInterface $schema
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function create(ConcreteValueInterface $schema);
}
