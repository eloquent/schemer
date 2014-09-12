<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Constraint\Exception;

use Exception;

/**
 * The supplied schema specification is invalid.
 */
final class InvalidSchemaSpecificationException extends Exception
{
    /**
     * Construct a new invalid schema specification exception.
     *
     * @param mixed          $specification The specification.
     * @param Exception|null $cause         The cause, if available.
     */
    public function __construct($specification, Exception $cause = null)
    {
        $this->specification = $specification;

        parent::__construct(
            'The supplied schema specification is invalid.',
            0,
            $cause
        );
    }

    /**
     * Get the specification.
     *
     * @return mixed The specification.
     */
    public function specification()
    {
        return $this->specification;
    }

    private $specification;
}
