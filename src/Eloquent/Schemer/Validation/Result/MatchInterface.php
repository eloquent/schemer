<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Validation\Result;

interface MatchInterface
{
    /**
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function schema();

    /**
     * @return \Eloquent\Schemer\Pointer\PointerInterface
     */
    public function pointer();
}
