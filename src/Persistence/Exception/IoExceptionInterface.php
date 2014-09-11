<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Persistence\Exception;

/**
 * The interface implemented by I/O exceptions.
 */
interface IoExceptionInterface
{
    /**
     * Get the location.
     *
     * @return string|null The location, if available.
     */
    public function location();
}
