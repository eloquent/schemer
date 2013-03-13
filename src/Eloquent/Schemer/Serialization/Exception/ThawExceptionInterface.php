<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Exception;

interface ThawExceptionInterface
{
    /**
     * @param string $data
     *
     * @return mixed
     */
    public function thaw($data);
}
