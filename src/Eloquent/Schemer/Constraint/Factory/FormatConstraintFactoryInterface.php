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

interface FormatConstraintFactoryInterface
{
    /**
     * @param string $key
     *
     * @return \Eloquent\Schemer\Constraint\FormatConstraintInterface|null
     */
    public function create($key);
}
